## Pizzario
#### **Managing API**

Api provides access to managing funcionality for stuff.

1. [**Menu management**](#menu)
1. [**Order management**](#orders)
1. [**Statistics area**](#stat)
 
#### <a name="menu"></a>Menu management
##### Categories
Menu categories help to navigate between different types of menu positions.
###### Categories list
Category(es) can be founded by name
###### Category update
Category name, description and status can be updated
###### Category delete
Category can be deleted if no positions inside it
###### Category create
New category can be added. Name is required. Description is optional.

##### Positions
Positions is something that can be bought by client. 
Position always assigned to at least one category.
###### Positions list
Position can be found by category or name.
###### Position update
Position fields can be updated:
 - image
 - name
 - description
 - status(active/inactive)
 - list of categories (at least 1)
###### Position archieve
Position can be archieved. 
###### Position create
New position can be created.
- image
- name
- description
- At least one category must be specified
###### <a name="orders"></a>Orders management
Orders created by clients can be managed in this area.
###### Order list
List of orders can be filtered by status, date of create
###### Order update
Order can be updated if not sent or finished.
###### Order cancel
Order can be cancelled if not finished.
###### Order create
New orders can be created.

###### <a name="stat"></a>Statistics
###### Order statistics
Provide per-day statistics based on order statuses. 
Filtering  by date and status must be available.
