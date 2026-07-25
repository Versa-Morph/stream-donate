# Backlog

Planned work, not yet built. Update/reorder freely as priorities shift — unlike `docs/`, this file is expected to go stale and get rewritten. Move an item to `README.md`/`docs/` (as shipped behavior) once it's actually implemented; delete it from here at that point, don't leave a stale duplicate.

## 1. Widget Studio — full customization coverage

Not every widget exposes all its visual settings in `resources/views/streamer/widgets.blade.php` yet — some fields only exist as defaults in `Streamer::getWidgetSettings()` with no UI control. Needs an audit: for each widget (`alert`, `milestone`, `leaderboard`, `qr`, `subathon`, `running_text`), diff the default keys in `getWidgetSettings()` against what the Widget Studio form actually lets a streamer edit, then add the missing controls.

## 2. Automated payout disbursement (Midtrans Payout/Iris API) — architecture built, gated off

The architecture is built (see `docs/superpowers/specs/2026-07-26-automated-payout-disbursement-design.md` and `docs/superpowers/plans/2026-07-26-automated-payout-disbursement-implementation.md`): `PayoutGatewayInterface`/`ManualPayoutGateway`/`MidtransIrisGateway`, a `processing` payout status, `CheckPayoutDisbursementStatusJob` polling, and a bank-code dropdown (`config/banks.php`). It's gated behind `config('payout.automated_disbursement_enabled')`, default off — `ManualPayoutGateway` (no-op, matches the pre-existing manual flow) is what actually runs today.

Request shapes (endpoints, paths, body/query field names, the two-key creator/approver auth split) are now confirmed against Midtrans's official Iris Postman collection — see `app/Services/Payout/MidtransIrisGateway.php`'s class docblock. **Still not safe to enable:** the collection has no saved example responses for any endpoint, so every response-parsing line (validity flag, `reference_no` location, status field/values) is still an unconfirmed `// TODO` placeholder. Before ever setting `PAYOUT_AUTOMATED_DISBURSEMENT_ENABLED=true` anywhere real: call each of the three endpoints (`account_validation`, `payouts` create, `payouts/{reference_no}`) against a real sandbox account, inspect the actual JSON responses, and replace the flagged parsing lines. Also needs `MIDTRANS_IRIS_API_KEY` (creator) and `MIDTRANS_IRIS_APPROVER_API_KEY` (approver) both provisioned, plus real bank-account KYC.

