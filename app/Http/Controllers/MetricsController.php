<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Ticket;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use ZipArchive;

class MetricsController extends Controller
{
    private const SOLD_STATUSES = ['VALID', 'USED', 'SOLD'];

    public function index(Request $request)
    {
        return view('metrics.index', $this->metricsData($request));
    }

    public function export(Request $request)
    {
        $data = $this->metricsData($request);
        $selectedEvent = $data['selectedEventId'] === 'all'
            ? 'Todos los eventos'
            : ($data['eventOptions']->firstWhere('id', $data['selectedEventId'])?->title ?? 'Evento seleccionado');

        $filePath = $this->buildWorkbook([
            'Resumen' => [
                ['Campo', 'Valor'],
                ['Desde', $data['from']],
                ['Hasta', $data['to']],
                ['Evento', $selectedEvent],
                ['Boletas vendidas', $data['ticketsSold']],
                ['Usuarios nuevos', $data['newUsers']],
                ['Usuarios registrados', $data['totalUsers']],
                ['Ingresos vendidos', $data['totalRevenue']],
                ['Ocupacion promedio', $data['avgOccupancy'] . '%'],
            ],
            'Tickets vendidos' => array_merge(
                [['Ticket', 'Fecha compra', 'Estado', 'Evento', 'Cliente', 'Zona', 'Silla', 'Precio']],
                $data['soldTickets']->map(fn($ticket) => [
                    $ticket->id,
                    $ticket->purchased_at ? Carbon::parse($ticket->purchased_at)->format('Y-m-d H:i') : '',
                    $ticket->status,
                    $ticket->event_title,
                    $ticket->customer_email,
                    $ticket->area_name,
                    $ticket->seat_number,
                    (float) $ticket->price,
                ])->all()
            ),
            'Ventas por semana' => array_merge(
                [['Semana', 'Boletas vendidas']],
                $data['ticketsByWeek']->map(fn($row) => [$row['week'], $row['total']])->all()
            ),
            'Ocupacion por evento' => array_merge(
                [['Evento', 'Fecha', 'Capacidad', 'Vendidas', 'Ingresos', 'Ocupacion %', 'Imagen']],
                $data['eventMetrics']->map(fn($row) => [
                    $row['title'],
                    $row['date'],
                    $row['capacity'],
                    $row['sold'],
                    $row['revenue'],
                    $row['occupancy'],
                    $row['poster_url'],
                ])->all()
            ),
        ]);

        $filename = "metricas_{$data['from']}_{$data['to']}.xlsx";

        return response()->download($filePath, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }

    private function metricsData(Request $request): array
    {
        $from = $request->input('from', now()->startOfMonth()->toDateString());
        $to = $request->input('to', now()->toDateString());
        $selectedEventId = $request->input('event_id', 'all');
        $fromDate = Carbon::parse($from)->startOfDay();
        $toDate = Carbon::parse($to)->endOfDay();

        $newUsers = User::whereBetween('created_at', [$fromDate, $toDate])->count();
        $totalUsers = User::count();
        $eventOptions = Event::orderByDesc('event_date')->get(['id', 'title', 'event_date']);

        $soldTickets = DB::table('tickets')
            ->leftJoin('events', 'events.id', '=', 'tickets.event_id')
            ->leftJoin('users', 'users.id', '=', 'tickets.user_id')
            ->leftJoin('area_seats', 'area_seats.ticket_id', '=', 'tickets.id')
            ->leftJoin('event_area', 'event_area.id', '=', 'area_seats.event_area_id')
            ->whereBetween('tickets.purchased_at', [$fromDate, $toDate])
            ->whereRaw('UPPER(tickets.status) IN (?, ?, ?)', self::SOLD_STATUSES)
            ->orderByDesc('tickets.purchased_at')
            ->select([
                'tickets.id',
                'tickets.event_id',
                'tickets.purchased_at',
                'tickets.status',
                'events.title as event_title',
                'users.email as customer_email',
                'event_area.area_name',
                'area_seats.seat_number',
                DB::raw('COALESCE(event_area.price, 0) as price'),
            ])
            ->when($selectedEventId !== 'all', fn($query) => $query->where('tickets.event_id', $selectedEventId))
            ->get();

        $ticketsSold = $soldTickets->count();
        $totalRevenue = (float) $soldTickets->sum('price');
        $revenueByEvent = $soldTickets
            ->groupBy('event_id')
            ->map(fn($tickets) => (float) $tickets->sum('price'));

        $ticketsByWeek = Ticket::select(
                DB::raw("DATE_TRUNC('week', purchased_at) as week"),
                DB::raw('COUNT(*) as total')
            )
            ->whereBetween('purchased_at', [$fromDate, $toDate])
            ->whereRaw('UPPER(status) IN (?, ?, ?)', self::SOLD_STATUSES)
            ->when($selectedEventId !== 'all', fn($query) => $query->where('event_id', $selectedEventId))
            ->groupBy('week')
            ->orderBy('week')
            ->get()
            ->map(fn($r) => [
                'week' => Carbon::parse($r->week)->format('d M'),
                'total' => $r->total,
            ]);

        $eventMetrics = Event::withCount([
                'tickets as sold_count' => fn($q) => $q
                    ->whereBetween('purchased_at', [$fromDate, $toDate])
                    ->whereRaw('UPPER(status) IN (?, ?, ?)', self::SOLD_STATUSES),
            ])
            ->when($selectedEventId !== 'all', fn($query) => $query->where('id', $selectedEventId))
            ->orderByDesc('event_date')
            ->get()
            ->map(fn($e) => [
                'id' => $e->id,
                'title' => $e->title,
                'date' => $e->event_date ? Carbon::parse($e->event_date)->format('Y-m-d H:i') : '',
                'poster_url' => $e->poster_url,
                'capacity' => $e->total_capacity,
                'sold' => $e->sold_count,
                'revenue' => $revenueByEvent->get($e->id, 0),
                'occupancy' => $e->total_capacity > 0
                    ? round(($e->sold_count / $e->total_capacity) * 100, 1)
                    : 0,
            ]);

        return [
            'ticketsSold' => $ticketsSold,
            'newUsers' => $newUsers,
            'totalUsers' => $totalUsers,
            'totalRevenue' => $totalRevenue,
            'soldTickets' => $soldTickets,
            'ticketsByWeek' => $ticketsByWeek,
            'occupancyData' => $eventMetrics,
            'eventMetrics' => $eventMetrics,
            'eventOptions' => $eventOptions,
            'selectedEventId' => $selectedEventId,
            'avgOccupancy' => $eventMetrics->avg('occupancy') ?? 0,
            'from' => $from,
            'to' => $to,
        ];
    }

    private function buildWorkbook(array $sheets): string
    {
        $directory = storage_path('app');
        if (!is_dir($directory)) {
            mkdir($directory, 0775, true);
        }

        $temp = tempnam($directory, 'metrics_');
        $filePath = $temp . '.xlsx';
        rename($temp, $filePath);

        $zip = new ZipArchive();
        if ($zip->open($filePath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            abort(500, 'No se pudo crear el archivo de metricas.');
        }
        $zip->addFromString('[Content_Types].xml', $this->contentTypesXml(count($sheets)));
        $zip->addFromString('_rels/.rels', $this->rootRelsXml());
        $zip->addFromString('docProps/app.xml', $this->appXml());
        $zip->addFromString('docProps/core.xml', $this->coreXml());
        $zip->addFromString('xl/workbook.xml', $this->workbookXml(array_keys($sheets)));
        $zip->addFromString('xl/_rels/workbook.xml.rels', $this->workbookRelsXml(count($sheets)));
        $zip->addFromString('xl/styles.xml', $this->stylesXml());

        $index = 1;
        foreach ($sheets as $rows) {
            $zip->addFromString("xl/worksheets/sheet{$index}.xml", $this->sheetXml($rows));
            $index++;
        }

        $zip->close();

        return $filePath;
    }

    private function contentTypesXml(int $sheetCount): string
    {
        $sheetOverrides = '';
        for ($i = 1; $i <= $sheetCount; $i++) {
            $sheetOverrides .= '<Override PartName="/xl/worksheets/sheet' . $i . '.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>';
        }

        return '<?xml version="1.0" encoding="UTF-8"?>'
            . '<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'
            . '<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>'
            . '<Default Extension="xml" ContentType="application/xml"/>'
            . '<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>'
            . '<Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>'
            . '<Override PartName="/docProps/core.xml" ContentType="application/vnd.openxmlformats-package.core-properties+xml"/>'
            . '<Override PartName="/docProps/app.xml" ContentType="application/vnd.openxmlformats-officedocument.extended-properties+xml"/>'
            . $sheetOverrides
            . '</Types>';
    }

    private function rootRelsXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8"?>'
            . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>'
            . '<Relationship Id="rId2" Type="http://schemas.openxmlformats.org/package/2006/relationships/metadata/core-properties" Target="docProps/core.xml"/>'
            . '<Relationship Id="rId3" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/extended-properties" Target="docProps/app.xml"/>'
            . '</Relationships>';
    }

    private function workbookXml(array $sheetNames): string
    {
        $sheetsXml = '';
        foreach ($sheetNames as $index => $name) {
            $id = $index + 1;
            $sheetsXml .= '<sheet name="' . $this->xml($name) . '" sheetId="' . $id . '" r:id="rId' . $id . '"/>';
        }

        return '<?xml version="1.0" encoding="UTF-8"?>'
            . '<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">'
            . '<sheets>' . $sheetsXml . '</sheets>'
            . '</workbook>';
    }

    private function workbookRelsXml(int $sheetCount): string
    {
        $rels = '';
        for ($i = 1; $i <= $sheetCount; $i++) {
            $rels .= '<Relationship Id="rId' . $i . '" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet' . $i . '.xml"/>';
        }
        $rels .= '<Relationship Id="rId' . ($sheetCount + 1) . '" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>';

        return '<?xml version="1.0" encoding="UTF-8"?>'
            . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            . $rels
            . '</Relationships>';
    }

    private function sheetXml(array $rows): string
    {
        $rowsXml = '';
        foreach ($rows as $rowNumber => $row) {
            $excelRow = $rowNumber + 1;
            $rowsXml .= '<row r="' . $excelRow . '">';
            foreach (array_values($row) as $columnIndex => $value) {
                $cell = $this->columnName($columnIndex + 1) . $excelRow;
                $style = $excelRow === 1 ? ' s="1"' : '';
                if (is_int($value) || is_float($value)) {
                    $rowsXml .= '<c r="' . $cell . '"' . $style . '><v>' . $value . '</v></c>';
                } else {
                    $rowsXml .= '<c r="' . $cell . '" t="inlineStr"' . $style . '><is><t>' . $this->xml((string) $value) . '</t></is></c>';
                }
            }
            $rowsXml .= '</row>';
        }

        return '<?xml version="1.0" encoding="UTF-8"?>'
            . '<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
            . '<cols><col min="1" max="12" width="24" customWidth="1"/></cols>'
            . '<sheetData>' . $rowsXml . '</sheetData>'
            . '</worksheet>';
    }

    private function stylesXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8"?>'
            . '<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
            . '<fonts count="2"><font><sz val="11"/><name val="Calibri"/></font><font><b/><sz val="11"/><color rgb="FFFFFFFF"/><name val="Calibri"/></font></fonts>'
            . '<fills count="3"><fill><patternFill patternType="none"/></fill><fill><patternFill patternType="gray125"/></fill><fill><patternFill patternType="solid"><fgColor rgb="FF009990"/><bgColor indexed="64"/></patternFill></fill></fills>'
            . '<borders count="1"><border><left/><right/><top/><bottom/><diagonal/></border></borders>'
            . '<cellStyleXfs count="1"><xf numFmtId="0" fontId="0" fillId="0" borderId="0"/></cellStyleXfs>'
            . '<cellXfs count="2"><xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0"/><xf numFmtId="0" fontId="1" fillId="2" borderId="0" xfId="0" applyFont="1" applyFill="1"/></cellXfs>'
            . '<cellStyles count="1"><cellStyle name="Normal" xfId="0" builtinId="0"/></cellStyles>'
            . '</styleSheet>';
    }

    private function appXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8"?>'
            . '<Properties xmlns="http://schemas.openxmlformats.org/officeDocument/2006/extended-properties" xmlns:vt="http://schemas.openxmlformats.org/officeDocument/2006/docPropsVTypes">'
            . '<Application>Andromeda Admin</Application>'
            . '</Properties>';
    }

    private function coreXml(): string
    {
        $now = now()->utc()->format('Y-m-d\TH:i:s\Z');

        return '<?xml version="1.0" encoding="UTF-8"?>'
            . '<cp:coreProperties xmlns:cp="http://schemas.openxmlformats.org/package/2006/metadata/core-properties" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:dcterms="http://purl.org/dc/terms/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">'
            . '<dc:title>Metricas administrativas</dc:title>'
            . '<dc:creator>Andromeda Admin</dc:creator>'
            . '<dcterms:created xsi:type="dcterms:W3CDTF">' . $now . '</dcterms:created>'
            . '<dcterms:modified xsi:type="dcterms:W3CDTF">' . $now . '</dcterms:modified>'
            . '</cp:coreProperties>';
    }

    private function columnName(int $number): string
    {
        $name = '';
        while ($number > 0) {
            $number--;
            $name = chr(65 + ($number % 26)) . $name;
            $number = intdiv($number, 26);
        }

        return $name;
    }

    private function xml(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES | ENT_XML1, 'UTF-8');
    }
}
