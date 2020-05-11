# Export data Click n Collect 

## To add the data of click n collect in your csv export, json etc ... 

1. install this [export library](https://packagist.org/packages/friendsofsylius/sylius-import-export-plugin) and add this : 

### this part is used to add the definition of new fields defined by click n collect

```yaml
# config/services.yaml
services:
    sylius.exporter.plugin.resource.orders:
        class: CoopTilleuls\SyliusClickNCollectPlugin\Exporter\Plugin\OrderResourcePlugin
        arguments:
            - "@sylius.repository.order"
            - "@property_accessor"
            - "@doctrine.orm.entity_manager"
            - "@sylius.service.address_concatenation"
            - "@sylius.exporter.orm.hydrator.orders"
```

### this part allows to add the new click n collect fields in the different export types

```yaml
# config/services.yaml
services:
    sylius.exporter.pluginpool.orders:
        class: FriendsOfSylius\SyliusImportExportPlugin\Exporter\Plugin\PluginPool
        arguments:
            - ["@sylius.exporter.plugin.resource.orders"]
            - ["Number", "ClickNCollect_Location_Name", "ClickNCollect_Location_Countrycode", "ClickNCollect_Location_Street", "ClickNCollect_Location_City",  "ClickNCollect_Location_PostCode", "ClickNCollect_Pin", "ClickNCollect_CollectionTime"  ]
```

### finally, this part will be used to rename the keys of the fields defined above.  

```yaml
# config/services.yaml
services:
    sylius.exporter.orders.csv:
        class: FriendsOfSylius\SyliusImportExportPlugin\Exporter\ResourceExporter
        arguments:
            - "@sylius.exporter.csv_writer"
            - "@sylius.exporter.pluginpool.orders"
            - ["Number", "ClickNCollect_Location_Name", "ClickNCollect_Location_Countrycode", "ClickNCollect_Location_Street", "ClickNCollect_Location_City",  "ClickNCollect_Location_PostCode", "ClickNCollect_Pin", "ClickNCollect_CollectionTime"  ]
            - "@sylius.exporters_transformer_pool"
        tags:
            - { name: sylius.exporter, type: sylius.order, format: csv }
    

``` 

To finish adding these services will overwrite the services of the same name already present by the import export plugin of sylius 

The basic definitions provided by the export plugin is available here :

[services_export_csv.yml](https://github.com/FriendsOfSylius/SyliusImportExportPlugin/blob/0557db82a609c72357de22aebc1210fd0043a10f/src/Resources/config/services_export_csv.yml) 

Here is the list of available fields: 
    
* ClickNCollect_Location_Name
* ClickNCollect_Location_Countrycode
* ClickNCollect_Location_Street
* ClickNCollect_Location_City
* ClickNCollect_Location_PostCode
* ClickNCollect_Pin
* ClickNCollect_CollectionTime
