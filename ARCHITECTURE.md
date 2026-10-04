# Architecture

## Overview

Pulsewatch is deployed on Railway as **four separate services** that work together:

1. **Web** — Laravel HTTP app (dashboard, auth, CRUD)
2. **Worker** — long-running `queue:work` process
3. **Cron** — long-running loop that runs `schedule:run` every minute
4. **PostgreSQL** — managed database

## Data Flow

### Ping Cycle

```
[User creates monitor]
        │
        ▼
[Cron service — every minute]
   php artisan monitors:dispatch
        │
        ▼
[Dispatches PingMonitor jobs to queue]
        │
        ▼
[Worker service pulls job]
        │
        ▼
[HTTP request to target URL]
        │
        ▼
[Store result in pings table]
        │
        ▼
[Compare with previous status]
        │
        ├── No change → done
        │
        └── Changed → send email alert + log to alert_logs
```

### Web Request Cycle

```
[User opens /monitors]
        │
        ▼
[Auth middleware]
        │
        ▼
[MonitorController@index]
        │
        ▼
[Query monitors WHERE user_id = auth()->id()]
        │
        ▼
[Eager load latestPing]
        │
        ▼
[Render view with status badges]
```

## Database Schema

### `users`
Standard Laravel users table.

### `monitors`
- `id`, `user_id` (FK), `name`, `url`, `method`, `interval_seconds`, `timeout_seconds`, `is_active`, timestamps
- Index: `(user_id)` for user-scoped queries

### `pings`
- `id`, `monitor_id` (FK), `status_code`, `response_time_ms`, `status` (up/down/degraded), `error_message`, `checked_at`, timestamps
- Index: `(monitor_id, checked_at)` — critical for time-range queries

### `alert_logs`
- `id`, `monitor_id` (FK), `type` (down/up), `channel`, `sent_at`, timestamps
- Index: `(monitor_id, sent_at)` — for audit trail

## Design Trade-offs

### Queue driver: Database vs Redis
**Chosen:** database queue.
**Why:** minimal dependencies, sufficient throughput for portfolio scale.
**Cost:** higher DB load at high job volume; migrate to Redis if scaling.

### Status determination
**Chosen:** HTTP status code + response time threshold (>3s = degraded).
**Why:** captures both correctness (up/down) and performance (degraded).
**Alternative:** keyword matching in response body (planned, not implemented).

### Alert triggering
**Chosen:** state transition detection.
**Why:** prevents alert spam during prolonged outages.
**Cost:** alert only fires when previous ping exists — first-ever ping that fails is silent.

### Multi-region pings
**Not implemented.** Currently pings from a single region (Railway's default).
**Next step:** distributed workers in different regions + median latency computation.

## Scaling Considerations

At 10,000 monitors with 60s intervals:
- **~167 ping jobs/second** — database queue starts becoming a bottleneck
- **Migrate to Redis** for queue driver
- **Horizontal worker scaling** — Railway supports multiple replicas
- **Ping results retention** — partition `pings` table by month, archive old data
- **Batch dispatch** — group monitors by interval to reduce scheduler overhead