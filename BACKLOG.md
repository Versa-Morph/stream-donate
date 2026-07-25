# Backlog

Planned work, not yet built. Update/reorder freely as priorities shift — unlike `docs/`, this file is expected to go stale and get rewritten. Move an item to `README.md`/`docs/` (as shipped behavior) once it's actually implemented; delete it from here at that point, don't leave a stale duplicate.

## 1. Widget Studio — full customization coverage

Not every widget exposes all its visual settings in `resources/views/streamer/widgets.blade.php` yet — some fields only exist as defaults in `Streamer::getWidgetSettings()` with no UI control. Needs an audit: for each widget (`alert`, `milestone`, `leaderboard`, `qr`, `subathon`, `running_text`), diff the default keys in `getWidgetSettings()` against what the Widget Studio form actually lets a streamer edit, then add the missing controls.

## 2. Automated payout disbursement (Midtrans Payout/Iris API) — architecture built, gated off

The architecture is built (see `docs/superpowers/specs/2026-07-26-automated-payout-disbursement-design.md` and `docs/superpowers/plans/2026-07-26-automated-payout-disbursement-implementation.md`): `PayoutGatewayInterface`/`ManualPayoutGateway`/`MidtransIrisGateway`, a `processing` payout status, `CheckPayoutDisbursementStatusJob` polling, and a bank-code dropdown (`config/banks.php`). It's gated behind `config('payout.automated_disbursement_enabled')`, default off — `ManualPayoutGateway` (no-op, matches the pre-existing manual flow) is what actually runs today.

**Not safe to enable yet:** `MidtransIrisGateway`'s three methods (`validateBankAccount`, `disburse`, `checkStatus`) have structurally-correct request/response wiring (auth, base URL, method shape) but every field name inside each Iris JSON payload is a flagged `// TODO: confirm field name` placeholder — the exact shape could not be confirmed against Midtrans's live Iris API reference in the session that built this. Before ever setting `PAYOUT_AUTOMATED_DISBURSEMENT_ENABLED=true` anywhere real: pull the current Iris API reference (`https://docs.midtrans.com/reference/`), replace every flagged field name in `app/Services/Payout/MidtransIrisGateway.php`, and test all three methods against a real sandbox account. Also needs the separate Midtrans Iris product enabled on the merchant account plus real bank-account KYC.

