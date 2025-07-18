<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Tickets Report</title>
    <style>
        @page {
            margin: 0;
            size: A4 landscape;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'SF Pro Display', 'Helvetica Neue', Helvetica, Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #1d1d1f;
            line-height: 1.47059;
            font-weight: 400;
            letter-spacing: -0.022em;
            min-height: 100vh;
            font-size: 11px;
        }
        
        .container {
            padding: 40px;
            background: #ffffff;
            margin: 20px;
            border-radius: 24px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            min-height: calc(100vh - 40px);
        }
        
        .header {
            text-align: center;
            margin-bottom: 48px;
            position: relative;
        }
        
        .header::before {
            content: '';
            position: absolute;
            top: -20px;
            left: 50%;
            transform: translateX(-50%);
            width: 60px;
            height: 6px;
            background: linear-gradient(90deg, #007AFF, #5856D6, #AF52DE);
            border-radius: 3px;
        }
        
        .header h1 {
            font-size: 42px;
            font-weight: 700;
            color: #1d1d1f;
            margin-bottom: 8px;
            letter-spacing: -0.025em;
            background: linear-gradient(135deg, #007AFF, #5856D6);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }
        
        .header .subtitle {
            font-size: 19px;
            color: #86868b;
            font-weight: 500;
            margin-bottom: 4px;
        }
        
        .header .timestamp {
            font-size: 15px;
            color: #86868b;
            font-weight: 400;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 24px;
            margin-bottom: 48px;
        }
        
        .stat-card {
            background: #f5f5f7;
            border-radius: 18px;
            padding: 24px 20px;
            text-align: center;
            position: relative;
            overflow: hidden;
            border: 1px solid #e5e5e7;
        }
        
        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            border-radius: 18px 18px 0 0;
        }
        
        .stat-card.total::before { background: linear-gradient(90deg, #007AFF, #5856D6); }
        .stat-card.resolved::before { background: linear-gradient(90deg, #30D158, #32D74B); }
        .stat-card.pending::before { background: linear-gradient(90deg, #FF9F0A, #FF9500); }
        .stat-card.other::before { background: linear-gradient(90deg, #86868b, #98989d); }
        
        .stat-number {
            font-size: 32px;
            font-weight: 700;
            color: #1d1d1f;
            display: block;
            margin-bottom: 6px;
            letter-spacing: -0.02em;
        }
        
        .stat-label {
            font-size: 13px;
            color: #86868b;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .section-title {
            font-size: 28px;
            font-weight: 700;
            color: #1d1d1f;
            margin-bottom: 24px;
            letter-spacing: -0.02em;
        }
        
        .table-container {
            background: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            border: 1px solid #e5e5e7;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
        }
        
        thead {
            background: linear-gradient(135deg, #f5f5f7 0%, #e5e5e7 100%);
        }
        
        th {
            padding: 16px 12px;
            text-align: left;
            font-weight: 600;
            color: #1d1d1f;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 2px solid #e5e5e7;
        }
        
        td {
            padding: 16px 12px;
            border-bottom: 1px solid #f5f5f7;
            vertical-align: middle;
        }
        
        tr:hover {
            background-color: #fbfbfd;
        }
        
        tr:last-child td {
            border-bottom: none;
        }
        
        .ticket-number {
            font-weight: 600;
            color: #007AFF;
            font-family: 'SF Mono', Monaco, 'Cascadia Code', 'Roboto Mono', Consolas, 'Courier New', monospace;
            font-size: 10px;
        }
        
        .status-badge {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 10px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: inline-block;
        }
        
        .status-open {
            background: rgba(0, 122, 255, 0.1);
            color: #007AFF;
            border: 1px solid rgba(0, 122, 255, 0.2);
        }
        
        .status-in-progress {
            background: rgba(255, 159, 10, 0.1);
            color: #FF9F0A;
            border: 1px solid rgba(255, 159, 10, 0.2);
        }
        
        .status-resolved {
            background: rgba(48, 209, 88, 0.1);
            color: #30D158;
            border: 1px solid rgba(48, 209, 88, 0.2);
        }
        
        .status-closed {
            background: rgba(134, 134, 139, 0.1);
            color: #86868b;
            border: 1px solid rgba(134, 134, 139, 0.2);
        }
        
        .priority-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            display: inline-block;
        }
        
        .priority-low {
            background: rgba(134, 134, 139, 0.1);
            color: #86868b;
        }
        
        .priority-medium {
            background: rgba(255, 204, 0, 0.1);
            color: #FFCC00;
        }
        
        .priority-high {
            background: rgba(255, 69, 58, 0.1);
            color: #FF453A;
        }
        
        .priority-critical {
            background: rgba(255, 69, 58, 0.2);
            color: #FF453A;
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.7; }
        }
        
        .subject-text {
            max-width: 200px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            color: #1d1d1f;
            font-weight: 500;
        }
        
        .user-name {
            font-weight: 500;
            color: #1d1d1f;
        }
        
        .date-text {
            color: #86868b;
            font-size: 10px;
            font-family: 'SF Mono', Monaco, monospace;
        }
        
        .no-data {
            text-align: center;
            padding: 80px 40px;
            color: #86868b;
        }
        
        .no-data-icon {
            font-size: 64px;
            margin-bottom: 16px;
            opacity: 0.3;
        }
        
        .no-data-text {
            font-size: 21px;
            font-weight: 500;
            color: #86868b;
            margin-bottom: 8px;
        }
        
        .no-data-subtitle {
            font-size: 15px;
            color: #98989d;
        }
        
        .footer {
            margin-top: 48px;
            text-align: center;
            color: #98989d;
            font-size: 11px;
        }
        
        .watermark {
            position: fixed;
            bottom: 20px;
            right: 20px;
            opacity: 0.1;
            font-size: 10px;
            color: #86868b;
            transform: rotate(-45deg);
        }
    </style>
</head>
<body>
    <div class="watermark">CONFIDENTIAL</div>
    
    <div class="container">
        <div class="header">
            <h1>Tickets Report</h1>
            <div class="subtitle">{{ $stats['period'] ?? 'Comprehensive Analysis' }}</div>
            <div class="timestamp">Generated {{ date('F j, Y \a\t g:i A') }}</div>
        </div>

        <div class="stats-grid">
            <div class="stat-card total">
                <span class="stat-number">{{ $stats['total_tickets'] ?? 0 }}</span>
                <span class="stat-label">Total Tickets</span>
            </div>
            <div class="stat-card resolved">
                <span class="stat-number">{{ $stats['resolved_tickets'] ?? 0 }}</span>
                <span class="stat-label">Resolved</span>
            </div>
            <div class="stat-card pending">
                <span class="stat-number">{{ $stats['pending_tickets'] ?? 0 }}</span>
                <span class="stat-label">Pending</span>
            </div>
            <div class="stat-card other">
                <span class="stat-number">{{ ($stats['total_tickets'] ?? 0) - ($stats['resolved_tickets'] ?? 0) - ($stats['pending_tickets'] ?? 0) }}</span>
                <span class="stat-label">Other</span>
            </div>
        </div>

        @if($tickets && count($tickets) > 0)
            <h2 class="section-title">Detailed Ticket Analysis</h2>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th style="width: 12%;">Ticket ID</th>
                            <th style="width: 28%;">Subject</th>
                            <th style="width: 15%;">Client</th>
                            <th style="width: 15%;">Agent</th>
                            <th style="width: 12%;">Status</th>
                            <th style="width: 10%;">Priority</th>
                            <th style="width: 8%;">Created</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tickets as $ticket)
                        <tr>
                            <td>
                                <span class="ticket-number">{{ $ticket->ticket_number ?? 'N/A' }}</span>
                            </td>
                            <td>
                                <div class="subject-text" title="{{ $ticket->subject ?? 'N/A' }}">
                                    {{ $ticket->subject ? \Illuminate\Support\Str::limit($ticket->subject, 35) : 'N/A' }}
                                </div>
                            </td>
                            <td>
                                <span class="user-name">{{ optional(optional($ticket->client)->user)->name ?? 'N/A' }}</span>
                            </td>
                            <td>
                                <span class="user-name">{{ optional(optional($ticket->agent)->user)->name ?? 'Unassigned' }}</span>
                            </td>
                            <td>
                                <span class="status-badge status-{{ strtolower(str_replace(' ', '-', $ticket->status ?? '')) }}">
                                    {{ $ticket->status ?? 'N/A' }}
                                </span>
                            </td>
                            <td>
                                <span class="priority-badge priority-{{ strtolower($ticket->priority ?? '') }}">
                                    {{ $ticket->priority ?? 'N/A' }}
                                </span>
                            </td>
                            <td>
                                <span class="date-text">{{ $ticket->created_at ? $ticket->created_at->format('M j, Y') : 'N/A' }}</span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="no-data">
                <div class="no-data-icon">📊</div>
                <div class="no-data-text">No tickets found</div>
                <div class="no-data-subtitle">Try adjusting your date range or filters</div>
            </div>
        @endif
        
        <div class="footer">
            <p>This report contains confidential information. Please handle accordingly.</p>
        </div>
    </div>
</body>
</html>