# SailCMS - Algolia Adapter

This is the official Algolia adapter for SailCMS. This adapters works like every other adapters for search. For the documentation on that, please read the search section of the SailCMS documentation.



## Installing

```bash
php sail install:official leeroy/sail-algolia
```

This will install the package using composer and then update your composer file to autoload the package.

If you wish to install it manually, you and perform the following

```bash
composer require leeroy/sail-algolia
```

After that, you can add `Leeroy\\Search\\Algolia` to the search section of the sailcms property of your composer.json file. It should look something like this:

```json
"sailcms": {
  "containers": ["Spec"],
  "modules": [],
  "search": {
    "algolia": "Leeroy\\Search\\Algolia\\Adapter"
  }
}
```



## Configuration

When installed, you need to add the following to your `.env` file.

```
SEARCH_ENGINE=algolia
ALGOLIA_APPLICATION_ID=yourappid
ALGOLIA_ADMIN_API_KEY=yourapikey
ALGOLIA_INDEX=default_index
```



You can now enjoy Algolia on your site.