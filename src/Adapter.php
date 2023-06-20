<?php

namespace Leeroy\Search\Algolia;

use Algolia\AlgoliaSearch\Exceptions\MissingObjectId;
use SailCMS\Collection;
use SailCMS\Types\SearchResults;
use SailCMS\Contracts\SearchAdapter;
use Algolia\AlgoliaSearch\SearchClient;
use Algolia\AlgoliaSearch\SearchIndex;

class Adapter implements SearchAdapter
{
    private SearchClient $client;
    private SearchIndex $index;

    public function __construct()
    {
        $this->client = SearchClient::create(env('APPLICATION_ID'), env('ADMIN_API_KEY'));
        $this->index = $this->client->initIndex(env('ALGOLIA_INDEX', 'data'));
    }

    /**
     *
     * Store a document in the search engine
     *
     * @param array|object $document
     * @param string $dataIndex
     * @return void
     *
     * @throws MissingObjectId
     */
    public function store(array|object $document, string $dataIndex = ''): void
    {
        if ($document instanceof Collection) {
            $document = $document->unwrap();
        } else {
            $document = (array)$document;
        }

        if (empty($dataIndex)) {
            $dataIndex = env('ALGOLIA_INDEX', 'data');
        }

        // Add id to make better use of the add/replace feature
        if (!empty($document['_id'])) {
            $document['id'] = (string)$document['_id'];
        }

        $this->client->initIndex($dataIndex)->saveObjects($document);
    }

    /**
     *
     * Delete a document from the database
     *
     * @param  string  $id
     * @param  string  $dataIndex
     * @return void
     *
     */
    public function remove(string $id, string $dataIndex = ''): void
    {
        if (empty($dataIndex)) {
            $dataIndex = env('ALGOLIA_INDEX', 'default');
        }

        $this->client->initIndex($dataIndex)->deleteObject($id);
    }

    /**
     *
     * Search Algolia for given keywords
     *
     * @param string $search
     * @param array $meta
     * @param string $dataIndex
     * @return SearchResults
     *
     */
    public function search(string $search, array $meta = [], string $dataIndex = ''): SearchResults
    {
        if ($dataIndex === '') {
            $dataIndex = env('ALGOLIA_INDEX', 'data');
        }

        // Set the index in which to search
        $this->index = $this->client->initIndex($dataIndex);
        $results = $this->index->search($search, $meta);

        return new SearchResults($results->getHits(), $results->getHitsCount());
    }

    /**
     *
     * Update filterable attributes
     *
     * Note: Execute this using the "execute" method on the SailCMS\Search class.
     *
     * @param string $index
     * @param array $fields
     * @return bool
     *
     */
    public function updateFilterable(string $index, array $fields = []): bool
    {
        return true;
    }

    /**
     *
     * Update sortable attributes
     *
     * Note: Execute this using the "execute" method on the SailCMS\Search class
     *
     * @param string $index
     * @param array $fields
     * @return bool
     *
     */
    public function updateSortable(string $index, array $fields = []): bool
    {
        return true;
    }

    /**
     *
     * Return the instance of Algolia for more custom requirements
     *
     * @return SearchClient
     *
     */
    public function getRawAdapter(): SearchClient
    {
        return $this->client;
    }

    /**
     *
     * Add given mock data for testing or development
     *
     * @param array $list
     * @return void
     *
     * @throws MissingObjectId
     */
    public function addMockData(array $list): void
    {
        $this->index->saveObjects($list);
    }
}