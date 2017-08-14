Temporal documentation for Production Migration
===============================================

## Upgrade Database

- Resolve updateAt field in fos_user_user:

```UPDATE fos_user_user SET updated_at = created_at WHERE updated_at = 0```

- e_suggestion content table need be reviewed, it has any rows with bug

- Remove civiclub userPreferences (active another field):
    ```UPDATE `user_setting` SET `enabled` = '1' WHERE `user_setting`.`id` = 2;```
    
- Review and Update catalogItems