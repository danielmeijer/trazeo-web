Temporal documentation for Production Migration
===============================================

## Upgrade Database

- Resolve updateAt field in fos_user_user:

```UPDATE fos_user_user SET updated_at = created_at WHERE updated_at = 0```
