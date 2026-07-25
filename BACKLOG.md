# Backlog

Planned work, not yet built. Update/reorder freely as priorities shift — unlike `docs/`, this file is expected to go stale and get rewritten. Move an item to `README.md`/`docs/` (as shipped behavior) once it's actually implemented; delete it from here at that point, don't leave a stale duplicate.

## 1. Widget Studio — full customization coverage

Not every widget exposes all its visual settings in `resources/views/streamer/widgets.blade.php` yet — some fields only exist as defaults in `Streamer::getWidgetSettings()` with no UI control. Needs an audit: for each widget (`alert`, `milestone`, `leaderboard`, `qr`, `subathon`, `running_text`), diff the default keys in `getWidgetSettings()` against what the Widget Studio form actually lets a streamer edit, then add the missing controls.

## 2. Automated payout disbursement (Midtrans Payout/Iris API) — architecture built, gated off

The architecture is built (see `docs/superpowers/specs/2026-07-26-automated-payout-disbursement-design.md` and `docs/superpowers/plans/2026-07-26-automated-payout-disbursement-implementation.md`): `PayoutGatewayInterface`/`ManualPayoutGateway`/`MidtransIrisGateway`, a `processing` payout status, `CheckPayoutDisbursementStatusJob` polling, and a bank-code dropdown (`config/banks.php`). It's gated behind `config('payout.automated_disbursement_enabled')`, default off — `ManualPayoutGateway` (no-op, matches the pre-existing manual flow) is what actually runs today.

Every request/response shape in `MidtransIrisGateway` is now confirmed live against the Iris sandbox (not just docs) — see that class's docblock and `docs/superpowers/plans/2026-07-26-automated-payout-disbursement-implementation.md`'s Task 5 for the exact `curl` calls and responses. No placeholder field names remain. Confirmed the two-key creator/approver split is real server-side enforcement (creator key gets HTTP 401 on approve/reject).

**Still not enabled anywhere:** this was verified against Midtrans's shared sandbox, not a production account with real bank-account KYC. Before ever setting `PAYOUT_AUTOMATED_DISBURSEMENT_ENABLED=true` in production: provision `MIDTRANS_IRIS_API_KEY` (creator) and `MIDTRANS_IRIS_APPROVER_API_KEY` (approver) for the real merchant account, complete Iris KYC, and run one real payout manually before trusting it broadly — `account_validation` in particular is known to always report success in sandbox regardless of the account given, so its real-account behavior is still unverified.

