--- ExpressionEngine Open API v0.4 ---
---
The Open API is an ExpressionEngine add-on that brings front-end CRUD capability to websites.
---

--
Authentication
--
Authenticate member with username and get a session_id that can be used for requests that require member authentication

| Request parameters  |  |  |
| :-------------  | ------------- | ------------- |
| username | The username of the member to authenticate | required |
| password | The password of the member to authenticate | required |
POST /authenticate_username
{ 
  "username": "michael",
  "password": "abc123"
}
< 200
< Content-Type: application/json
{
  "session_id": "87b1bc8c82c40214d8682c9cbada3e91f2df6b98",
  "member_id": "1",
  "username": "testuser",
  "screen_name": "Test User"
}

Authenticate member with email and get a session_id that can be used for requests that require member authentication

| Request parameters  |  |  |
| :-------------  | ------------- | ------------- |
| email | The email of the member to authenticate | required |
| password | The password of the member to authenticate | required |
POST /authenticate_email
{ 
  "email": "michael@five.com",
  "password": "abc123"
}
< 200
< Content-Type: application/json
{
  "session_id": "87b1bc8c82c40214d8682c9cbada3e91f2df6b98",
  "member_id": "1",
  "username": "testuser",
  "screen_name": "Test User"
}

Authenticate member with id and get a session_id that can be used for requests that require member authentication

| Request parameters  |  |  |
| :-------------  | ------------- | ------------- |
| member_id | The id of the member to authenticate | required |
| password | The password of the member to authenticate | required |
POST /authenticate_member_id
{ 
  "member_id": "1",
  "password": "abc123"
}
< 200
< Content-Type: application/json
{
  "session_id": "87b1bc8c82c40214d8682c9cbada3e91f2df6b98",
  "member_id": "1",
  "username": "testuser",
  "screen_name": "Test User"
}

--
Channels
--
Get channel

\* Requires authentication if specified in settings

| Request parameters  |  |  |
| :-------------  | ------------- | ------------- |
| channel_id | The id of the channel to get | required |
GET /get_channel
{ 
  "channel_id": "1"
}
< 200
< Content-Type: application/json
[
  {
    "channel_id": "1",
    "site_id": "1",
    "channel_name": "channel_name",
    "channel_title": "Channel",
    "channel_url": "http:\/\/www.ee2.com",
    "channel_description": "",
    "channel_lang": "en",
    "total_entries": "41",
    "total_comments": "33",
    "last_entry_date": "1360205184",
    "last_comment_date": "1350228795",
    "cat_group": "2",
    "status_group": null,
    "deft_status": "",
    "field_group": "1",
    "search_excerpt": null,
    "deft_category": "",
    "deft_comments": "y",
    "channel_require_membership": "y",
    "channel_max_chars": null,
    "channel_html_formatting": "all",
    "channel_allow_img_urls": "y",
    "channel_auto_link_urls": "y",
    "channel_notify": "n",
    "channel_notify_emails": "",
    "comment_url": "",
    "comment_system_enabled": "y",
    "comment_require_membership": "n",
    "comment_use_captcha": "n",
    "comment_moderate": "n",
    "comment_max_chars": "5000",
    "comment_timelock": "0",
    "comment_require_email": "n",
    "comment_text_formatting": "xhtml",
    "comment_html_formatting": "safe",
    "comment_allow_img_urls": "n",
    "comment_auto_link_urls": "y",
    "comment_notify": "n",
    "comment_notify_authors": "n",
    "comment_notify_emails": "",
    "comment_expiration": "0",
    "search_results_url": "",
    "ping_return_url": "",
    "show_button_cluster": "y",
    "rss_url": "",
    "enable_versioning": "n",
    "max_revisions": "10",
    "default_entry_title": "",
    "url_title_prefix": "",
    "live_look_template": "0"
  }
]

Get channels

\* Requires authentication if specified in settings

| Request parameters  |  |  |
| :-------------  | ------------- | ------------- |
| site_id | The id of the site to get channels from | optional |
GET /get_channels
{
  "site_id": "1"
}
< 200
< Content-Type: application/json
[
  {
    "channel_id": "1",
    "site_id": "1",
    "channel_name": "channel_name",
    "channel_title": "Channel",
    "channel_url": "http:\/\/www.ee2.com",
    "channel_description": "",
    "channel_lang": "en",
    "total_entries": "41",
    "total_comments": "33",
    "last_entry_date": "1360205184",
    "last_comment_date": "1350228795",
    "cat_group": "2",
    "status_group": null,
    "deft_status": "",
    "field_group": "1",
    "search_excerpt": null,
    "deft_category": "",
    "deft_comments": "y",
    "channel_require_membership": "y",
    "channel_max_chars": null,
    "channel_html_formatting": "all",
    "channel_allow_img_urls": "y",
    "channel_auto_link_urls": "y",
    "channel_notify": "n",
    "channel_notify_emails": "",
    "comment_url": "",
    "comment_system_enabled": "y",
    "comment_require_membership": "n",
    "comment_use_captcha": "n",
    "comment_moderate": "n",
    "comment_max_chars": "5000",
    "comment_timelock": "0",
    "comment_require_email": "n",
    "comment_text_formatting": "xhtml",
    "comment_html_formatting": "safe",
    "comment_allow_img_urls": "n",
    "comment_auto_link_urls": "y",
    "comment_notify": "n",
    "comment_notify_authors": "n",
    "comment_notify_emails": "",
    "comment_expiration": "0",
    "search_results_url": "",
    "ping_return_url": "",
    "show_button_cluster": "y",
    "rss_url": "",
    "enable_versioning": "n",
    "max_revisions": "10",
    "default_entry_title": "",
    "url_title_prefix": "",
    "live_look_template": "0"
  },
  {
    "channel_id": "2",
    "site_id": "1",
    "channel_name": "channel_name",
    "channel_title": "Channel",
    "channel_url": "http:\/\/www.ee2.com",
    "channel_description": "",
    "channel_lang": "en",
    "total_entries": "41",
    "total_comments": "33",
    "last_entry_date": "1360205184",
    "last_comment_date": "1350228795",
    "cat_group": "2",
    "status_group": null,
    "deft_status": "",
    "field_group": "1",
    "search_excerpt": null,
    "deft_category": "",
    "deft_comments": "y",
    "channel_require_membership": "y",
    "channel_max_chars": null,
    "channel_html_formatting": "all",
    "channel_allow_img_urls": "y",
    "channel_auto_link_urls": "y",
    "channel_notify": "n",
    "channel_notify_emails": "",
    "comment_url": "",
    "comment_system_enabled": "y",
    "comment_require_membership": "n",
    "comment_use_captcha": "n",
    "comment_moderate": "n",
    "comment_max_chars": "5000",
    "comment_timelock": "0",
    "comment_require_email": "n",
    "comment_text_formatting": "xhtml",
    "comment_html_formatting": "safe",
    "comment_allow_img_urls": "n",
    "comment_auto_link_urls": "y",
    "comment_notify": "n",
    "comment_notify_authors": "n",
    "comment_notify_emails": "",
    "comment_expiration": "0",
    "search_results_url": "",
    "ping_return_url": "",
    "show_button_cluster": "y",
    "rss_url": "",
    "enable_versioning": "n",
    "max_revisions": "10",
    "default_entry_title": "",
    "url_title_prefix": "",
    "live_look_template": "0"
  }
]

Create channel

\* Requires member authentication and correct permissions

| Request parameters  |  |  |
| :-------------  | ------------- | ------------- |
| channel_name | The short name of the new channel | required |
| channel_title | The title of the new channel | required |
| session_id | An authenticated session id | required if not using cookie |
POST /create_channel
{ 
  "channel_name": "new_channel", 
  "channel_title": "New Channel", 
  "session_id": "87b1bc8c82c40214d8682c9cbada3e91f2df6b98"
}
< 200
< Content-Type: application/json
{
  "channel_id": "1"
}

Update channel

\* Requires member authentication and correct permissions

| Request parameters  |  |  |
| :-------------  | ------------- | ------------- |
| channel_id | The id of the channel to update | required |
| session_id | An authenticated session id | required if not using cookie |
POST /update_channel
{ 
  "channel_id": "1",, 
  "session_id": "87b1bc8c82c40214d8682c9cbada3e91f2df6b98"
}
< 200
< Content-Type: application/json
{
  "channel_id": "1"
}

Delete channel

\* Requires member authentication and correct permissions

| Request parameters  |  |  |
| :-------------  | ------------- | ------------- |
| channel_id | The id of the channel to delete | required |
| session_id | An authenticated session id | required if not using cookie |
POST /delete_channel
{ 
  "channel_id": "1", 
  "session_id": "87b1bc8c82c40214d8682c9cbada3e91f2df6b98"
}
< 200
< Content-Type: application/json
{
  "channel_id": "1"
}


--
Channel Entries
--
Get channel entry

\* Requires authentication if specified in settings

| Request parameters  |  |  |
| :-------------  | ------------- | ------------- |
| entry_id | The id of the entry to get | required |
GET /get_channel_entry
{ 
  "entry_id": "1" 
}
< 200
< Content-Type: application/json
[
  {
    "entry_id": "1",
    "channel_id": "1",
    "author_id": "1",
    "title": "First Entry",
    "url_title": "first_entry",
    "entry_date": "1294706913",
    "expiration_date": "0",
    "status": "open",
    "custom_field_name": "Text"
  }
]

Get channel entries

\* Requires authentication if specified in settings

| Request parameters  |  |  |  |
| :-------------  | ------------- | ------------- | ------------- |
| channel_id | The id of the channel to get the entries of | optional |  |
| select | A comma separated string of fields to select | optional | defaults to all fields |
| where | An object of field value pairs to match entries against | optional |  |
| order_by | The field to order the entries by | optional | defaults to "channel_id" |
| sort | The sort order of the entries | optional | defaults to "desc"  |
| limit | The number of entries to get | optional |  |
| offset | The number of entries to offset by | optional |  |
GET /get_channel_entries
{ 
  "channel_id": "1",
  "select": "title, url_title, entry_date",
  "where": {
    "status": "open",
    "entry_date": "> 1368192049",
    "custom_field_name": "Text"
  },
  "order_by": "title",
  "sort": "asc",
  "limit": "10",
  "offset": "2"
}
< 200
< Content-Type: application/json
[
  {
    "entry_id": "1",
    "channel_id": "1",
    "author_id": "1",
    "title": "First Entry",
    "url_title": "first_entry",
    "entry_date": "1294706913",
    "expiration_date": "0",
    "status": "open",
    "custom_field_name": "Text"
  },
  {
    "entry_id": "2",
    "channel_id": "1",
    "author_id": "1",
    "title": "Second Entry",
    "url_title": "second_entry",
    "entry_date": "1295535329",
    "expiration_date": "0",
    "status": "open",
    "custom_field_name": "Text"
  }
]

Create channel entry

\* Requires member authentication and correct permissions

| Request parameters  |  |  |  |
| :-------------  | ------------- | ------------- | ------------- |
| channel_id | The id of the channel to create the entry in | required |  |
| url_title | The url title of the new entry | required |  |
| title | The title of the new entry | required |  |
| entry_date | The entry date of the new entry | optional | defaults to current time |
| edit_date | The edit date of the new entry | optional | defaults to current time |
| custom_field_name | Any custom field name | optional | required custom fields must be submitted |
| session_id | An authenticated session id | required if not using cookie |
POST /create_channel_entry
{
  "channel_id": "1",
  "url_title": "new_entry",
  "title": "New Entry",
  "entry_date": "12345678",
  "custom_field_name": "Text", 
  "session_id": "87b1bc8c82c40214d8682c9cbada3e91f2df6b98"
}
< 200
< Content-Type: application/json
{
  "entry_id": "1"
}

Update channel entry

\* Requires member authentication and correct permissions

| Request parameters  |  |  |
| :-------------  | ------------- | ------------- |
| channel_id | The id of the channel that the entry is in | required |  |
| entry_id | The id of the entry to update | required |
| url_title | The new url title of the entry | optional |
| title | The new title of the entry | optional |
| entry_date | The new entry date of the entry | optional |
| custom_field_name | Any custom field name | optional |
| session_id | An authenticated session id | required if not using cookie |
POST /update_channel_entry
{
  "channel_id": "1",
  "entry_id": "1",
  "url_title": "new_entry",
  "title": "New Entry",
  "entry_date": "12345678",
  "custom_field_name": "Text", 
  "session_id": "87b1bc8c82c40214d8682c9cbada3e91f2df6b98"
}
< 200
< Content-Type: application/json
{
  "entry_id": "1"
}

Delete channel entry

\* Requires member authentication and correct permissions

| Request parameters  |  |  |
| :-------------  | ------------- | ------------- |
| entry_id | The id of the entry or an array of entry ids to delete | required |
| session_id | An authenticated session id | required if not using cookie |
POST /delete_channel_entry
{ 
  "entry_id": "1", 
  "session_id": "87b1bc8c82c40214d8682c9cbada3e91f2df6b98"
}
< 200
< Content-Type: application/json
{
  "entry_id": "1"
}


--
Categories
--
Get category

\* Requires authentication if specified in settings

| Request parameters  |  |  |
| :-------------  | ------------- | ------------- |
| cat_id | The id of the category to get | required |
GET /get_category
{ 
  "cat_id": "1"
}
< 200
< Content-Type: application/json
[
  {
    "cat_id": "1",
    "site_id": "1",
    "group_id": "2",
    "parent_id": "0",
    "cat_name": "My Category",
    "cat_url_title": "my_category",
    "cat_description": "",
    "cat_image": "",
    "cat_order": "1"
  }
]

Get categories

\* Requires authentication if specified in settings

| Request parameters  |  |  |  |
| :-------------  | ------------- | ------------- | ------------- |
| select | A comma separated string of fields to select | optional | defaults to all fields |
| where | An object of field value pairs to match entries against | optional |  |
| order_by | The field to order the entries by | optional | defaults to "cat_order" |
| sort | The sort order of the entries | optional | defaults to "asc"  |
| limit | The number of entries to get | optional |  |
| offset | The number of entries to offset by | optional |  |
GET /get_categories
{
  "where": {
    "group_id": "1"
  },
  "order_by": "cat_orer",
  "sort": "asc",
  "limit": "10",
  "offset": "2"
}
< 200
< Content-Type: application/json
[
  {
    "cat_id": "1",
    "site_id": "1",
    "group_id": "1",
    "parent_id": "0",
    "cat_name": "First Category",
    "cat_url_title": "first_category",
    "cat_description": "",
    "cat_image": "",
    "cat_order": "1"
  },
  {
    "cat_id": "2",
    "site_id": "1",
    "group_id": "1",
    "parent_id": "0",
    "cat_name": "Second Category",
    "cat_url_title": "second_category",
    "cat_description": "",
    "cat_image": "",
    "cat_order": "2"
  }
]

Get categories by group

\* Requires authentication if specified in settings

| Request parameters  |  |  |
| :-------------  | ------------- | ------------- |
| group_id | The id of the category group to get the categories of | required |
GET /get_categories_by_group
{ 
  "group_id": "2"
}
< 200
< Content-Type: application/json
[
  {
    "cat_id": "1",
    "site_id": "1",
    "group_id": "2",
    "parent_id": "0",
    "cat_name": "My Category",
    "cat_url_title": "my_category",
    "cat_description": "",
    "cat_image": "",
    "cat_order": "1"
  },
  {
    "cat_id": "2",
    "site_id": "1",
    "group_id": "1",
    "parent_id": "0",
    "cat_name": "Second Category",
    "cat_url_title": "second_category",
    "cat_description": "",
    "cat_image": "",
    "cat_order": "2"
  }
]

Get categories by channel

\* Requires authentication if specified in settings

| Request parameters  |  |  |  |
| :-------------  | ------------- | ------------- | ------------- |
| channel_id | The id of the channel to get the categories of | required |  |
| select | A comma separated string of fields to select | optional | defaults to all fields |
| where | An object of field value pairs to match entries against | optional |  |
| order_by | The field to order the entries by | optional | defaults to "cat_order" |
| sort | The sort order of the entries | optional | defaults to "asc"  |
| limit | The number of entries to get | optional |  |
| offset | The number of entries to offset by | optional |  |
GET /get_categories_by_channel
{ 
  "channel_id": "1"
}
< 200
< Content-Type: application/json
[
  {
    "cat_id": "1",
    "site_id": "1",
    "group_id": "2",
    "parent_id": "0",
    "cat_name": "My Category",
    "cat_url_title": "my_category",
    "cat_description": "",
    "cat_image": "",
    "cat_order": "1"
  },
  {
    "cat_id": "2",
    "site_id": "1",
    "group_id": "1",
    "parent_id": "0",
    "cat_name": "Second Category",
    "cat_url_title": "second_category",
    "cat_description": "",
    "cat_image": "",
    "cat_order": "2"
  }
]

Create category

\* Requires member authentication and correct permissions

| Request parameters  |  |  |
| :-------------  | ------------- | ------------- |
| group_id | The id of the category group to create the category in | required |
| cat_url_title | The url title of the new category | required |
| cat_name | The name of the new category | required |
| site_id | The id of the site to create the category in | optional |
| parent_id | The id of the parent category | optional |
| cat_description | The description of the new category | optional |
| cat_order | The order of the new category | optional |
| session_id | An authenticated session id | required if not using cookie |
POST /create_category
{ 
  "group_id" : "1",
  "cat_url_title": "new_category", 
  "cat_name": "New Category",
  "site_id" : "1",
  "parent_id" : "0",
  "cat_description": "New Category Description",
  "cat_order" : "4", 
  "session_id": "87b1bc8c82c40214d8682c9cbada3e91f2df6b98"
}
< 200
< Content-Type: application/json
{
  "cat_id": "1"
}

Update category

\* Requires member authentication and correct permissions

| Request parameters  |  |  |
| :-------------  | ------------- | ------------- |
| cat_id | The id of the category to update | required |
| group_id | The id of the category group | optional |
| cat_url_title | The url title of the category | optional |
| cat_name | The name of the category | optional |
| parent_id | The id of the parent category | optional |
| cat_description | The description of the category | optional |
| cat_order | The order of the category | optional |
| session_id | An authenticated session id | required if not using cookie |
POST /update_category
{ 
  "cat_id": "1",
  "group_id" : "1",
  "cat_url_title": "new_category", 
  "cat_name": "New Category",
  "parent_id" : "0",
  "cat_description": "New Category Description",
  "cat_order" : "4", 
  "session_id": "87b1bc8c82c40214d8682c9cbada3e91f2df6b98"
}
< 200
< Content-Type: application/json
{
  "cat_id": "1"
}

Delete category

\* Requires member authentication and correct permissions

| Request parameters  |  |  |
| :-------------  | ------------- | ------------- |
| cat_id | The id of the category to delete | required |
| session_id | An authenticated session id | required if not using cookie |
POST /delete_category
{ 
  "cat_id": "1", 
  "session_id": "87b1bc8c82c40214d8682c9cbada3e91f2df6b98"
}
< 200
< Content-Type: application/json
{
  "cat_id": "1"
}

--
Category Groups
--
Get category group

\* Requires authentication if specified in settings

| Request parameters  |  |  |
| :-------------  | ------------- | ------------- |
| group_id | The id of the category group to get | required |
GET /get_category_group
{ 
  "group_id": "1"
}
< 200
< Content-Type: application/json
[
  {
    "group_id": "1",
    "site_id": "1",
    "group_name": "First Group",
    "sort_order": "a",
    "field_html_formatting": "all",
    "can_edit_categories": "",
    "can_delete_categories": "",
    "exclude_group": "0"
  }
]

Get category groups

\* Requires authentication if specified in settings
GET /get_category_groups
< 200
< Content-Type: application/json
[
  {
    "group_id": "1",
    "site_id": "1",
    "group_name": "First Group",
    "sort_order": "a",
    "field_html_formatting": "all",
    "can_edit_categories": "",
    "can_delete_categories": "",
    "exclude_group": "0"
  },
  {
    "group_id": "2",
    "site_id": "1",
    "group_name": "Second Group",
    "sort_order": "a",
    "field_html_formatting": "all",
    "can_edit_categories": "",
    "can_delete_categories": "",
    "exclude_group": "0"
  }
]

Create category group

\* Requires member authentication and correct permissions

| Request parameters  |  |  |
| :-------------  | ------------- | ------------- |
POST /create_category_group
{ 
}
< 200
< Content-Type: application/json
{
  "group_id": "1"
}

Update category group

\* Requires member authentication and correct permissions

| Request parameters  |  |  |
| :-------------  | ------------- | ------------- |
| group_id | The id of the category group to update | required |
POST /update_category_group
{ 
  "group_id" : "1"
}
< 200
< Content-Type: application/json
{
  "group_id": "1"
}

Delete category group

\* Requires member authentication and correct permissions

| Request parameters  |  |  |
| :-------------  | ------------- | ------------- |
| group_id | The id of the category group to delete | required |
POST /delete_category_group
{ 
  "group_id": "1"
}
< 200
< Content-Type: application/json
{
  "group_id": "1"
}

--
Members
--
Get member

\* Requires member authentication and correct permissions

| Request parameters  |  |  |
| :-------------  | ------------- | ------------- |
| member_id | The id of the member to get | required |
GET /get_member
{ 
  "member_id": "1"
}
< 200
< Content-Type: application/json
[
  {
      "member_id": "1", 
      "group_id": "1", 
      "email": "user@email.com", 
      "username":"test_user", 
      "screen_name": "Test User"
  }
]

Get members

\* Requires member authentication and correct permissions

| Request parameters  |  |  |  |
| :-------------  | ------------- | ------------- | ------------- |
| select | A comma separated string of fields to select | optional | defaults to all fields |
| where | An object of field value pairs to match entries against | optional |  |
| order_by | The field to order the entries by | optional | defaults to "member_id" |
| sort | The sort order of the entries | optional | defaults to "asc"  |
| limit | The number of entries to get | optional |  |
| offset | The number of entries to offset by | optional |  |
GET /get_members
{
  "where": {
    "group_id": "1"
  },
  "order_by": "member_id",
  "sort": "asc",
  "limit": "10",
  "offset": "0"
}
< 200
< Content-Type: application/json
[
  {
    "member_id": "1", 
    "group_id": "1", 
    "email": "user@email.com", 
    "username":"test_user", 
    "screen_name": "Test User"
  },
  {
    "member_id": "2", 
    "group_id": "1", 
    "email": "user2@email.com", 
    "username":"test_user_2", 
    "screen_name": "Test User 2"
  }
]

--
Member Groups
--
Get member group

\* Requires member authentication and correct permissions

| Request parameters  |  |  |
| :-------------  | ------------- | ------------- |
| group_id | The id of the member group to get | required |
GET /get_member_group
{ 
  "group_id": "1"
}
< 200
< Content-Type: application/json
[
  {
  }
]

Get member groups

\* Requires member authentication and correct permissions

| Request parameters  |  |  |  |
| :-------------  | ------------- | ------------- | ------------- |
| select | A comma separated string of fields to select | optional | defaults to all fields |
| where | An object of field value pairs to match entries against | optional |  |
| order_by | The field to order the entries by | optional | defaults to "group_id" |
| sort | The sort order of the entries | optional | defaults to "asc"  |
| limit | The number of entries to get | optional |  |
| offset | The number of entries to offset by | optional |  |
GET /get_member_groups
{
  "order_by": "group_id",
  "sort": "asc",
  "limit": "10",
  "offset": "0"
}
< 200
< Content-Type: application/json
[
  {
  }
]