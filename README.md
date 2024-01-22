# JetEngine - listing as filter
This plugin allows to use JetEngine listing as filter for another listing. Download and instal it as usual WordPress plugin.

# How to use

To use this plugin you need at least 2 listings on the page:
- **1st** - to use as filter.
- **2nd** - listing where you want to filter the data.

Go to the `Listing as Filter` section of 1st listing.

<img width="290" alt="image" src="https://github.com/Crocoblock/jet-engine-listing-as-filter/assets/4987981/c8738d4d-f742-4624-abae-4680073e1263">

Enable this feature. Than you need to select actual filter which will be used as base. In this case filter mainly required to get the query settings for the filter - type of query and query variable. Please note - without selected filter this functionality will not work. Also note - for select, radio, checkboxes or any other filter types which allow to set options, these options will be ignored, Listing will use it's own query settings for render.

Set unique `Query ID` for 1st listing, than set the same Query ID as CSS ID for 2nd listing and as Query ID for JetEngine Query Builder query (if used)

<img width="292" alt="image" src="https://github.com/Crocoblock/jet-engine-listing-as-filter/assets/4987981/c5a72673-11c5-411a-9369-9a279eb3079f">

<img width="307" alt="image" src="https://github.com/Crocoblock/jet-engine-listing-as-filter/assets/4987981/242c19ea-4e65-40ba-9046-a679811c7a82">

<img width="869" alt="image" src="https://github.com/Crocoblock/jet-engine-listing-as-filter/assets/4987981/9fd64b67-4723-46d5-b8f4-4941422c659a">

This part is required to help JetSmartFilters plugin to understand what listing on the page should be filterable.

At the moment indication of selected items in the 1st listing you need to do manually, with help of CSS.To do this use `.jet-listing-grid__item[is-checked="1"]` as parent selector for items you want to change. Also for easier finding required items with CSS, will be better to add custom CSS class to the top level element of your listing item:

<img width="1435" alt="image" src="https://github.com/Crocoblock/jet-engine-listing-as-filter/assets/4987981/e787be9a-1c62-4acd-85b1-0e7195ad6a06">

Example of such CSS

```CSS
.jet-listing-grid__item[is-checked="1"] .main-properties-container {
    border-color: #111;
}
```
