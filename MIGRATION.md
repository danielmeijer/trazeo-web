Temporal documentation for Production Migration
===============================================

## Upgrade Database

- Mailjet configuration
  
  We are going to migrate to mailjet system, configure it in parameters.yml and parameters_dev.yml

- Resolve updateAt field in fos_user_user:

```UPDATE fos_user_user SET updated_at = created_at WHERE updated_at = 0```

- e_suggestion content table need be reviewed, it has any rows with bug
```
    UPDATE `e_suggestion` SET `text` = 'access_groups' WHERE `e_suggestion`.`id` = 3;
    UPDATE `e_suggestion` SET `text` = 'complete_profile' WHERE `e_suggestion`.`id` = 2;
    DELETE FROM `e_suggestion` WHERE `e_suggestion`.`id` = 1
```

- Remove civiclub userPreferences (active another field):
    ```UPDATE `user_setting` SET `enabled` = '1' WHERE `user_setting`.`id` = 2;```
    
- Create web/uploads/images/catalog directory
```
    mkdir web/uploads/images/catalog -p
    chmod 777 web/uploads/images/catalog
```
    
- Review and Update catalogItems

- Update tables about slug field:

```
    sh scripts/schema_update.sh
```

```
   SET @r := 0;
   UPDATE  geo_city g
   SET     g.slug = (@r := @r + 1)
   ORDER BY
           RAND()
```
    sh scripts/schema_update.sh
    
```
   SET @r := 0;
   UPDATE  geo_state g
   SET     g.slug = (@r := @r + 1)
   ORDER BY
           RAND()
```

- Spool changed to: /smail/spool

If are using Docker, you need create it in Docker PHP container

```
    mkdir /smail/spool -p
    chmod 777 /smail/spool/default/
```

- Configure rabbit parameters