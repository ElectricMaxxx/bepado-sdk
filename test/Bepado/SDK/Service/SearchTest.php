<?php
/**
 * This file is part of the Bepado SDK Component.
 *
 * @version $Revision$
 */

namespace Bepado\SDK\Service;

use Bepado\Common;
use Bepado\SDK\HttpClient;
use Bepado\SDK\Struct;

require_once __DIR__ . '/../bootstrap.php';

class SearchTest extends Common\Test\TestCase
{
    const APIKEY = '1234';

    public function testVerify()
    {
        $searchService = new Search(
            $httpClient = $this->getMock('\\Bepado\\SDK\\HttpClient'),
            self::APIKEY
        );

        $httpClient
            ->expects($this->once())
            ->method('request')
            ->with(
                'GET',
                '/search?query=homme&apiKey=' . self::APIKEY . '&offset=0&limit=10'
            )
            ->will(
                $this->returnValue(
                    new HttpClient\Response(
                        array(
                            'status' => 200,
                            'body' => file_get_contents(__DIR__ . '/_fixtures/search_result.js'),
                        )
                    )
                )
            );

        $result = $searchService->search(
            new Struct\Search(
                array(
                    'query' => 'homme',
                )
            )
        );

        $this->assertTrue($result instanceof Struct\SearchResult);
        return $result;
    }

    public function getSearchResultExpectations()
    {
        return array(
            array('resultCount', 21),
            array('priceFrom', 12.89),
            array('priceTo', 89.85),
            array(
                'vendors',
                array (
                    'Joop!' => 4,
                    'Hugo Boss' => 3,
                    'Davidoff' => 2,
                    'Calvin Klein' => 1,
                    'Diesel' => 1,
                    'Dior' => 1,
                    'DoIce & Gabbana' => 1,
                    'Giorgio Armani' => 1,
                    'Givenchy' => 1,
                    'Issey Miyake' => 1,
                )
            ),
        );
    }

    /**
     * @depends testVerify
     * @dataProvider getSearchResultExpectations
     */
    public function testParseResult($key, $value, $result)
    {
        $this->assertEquals(
            $value,
            $result->$key
        );
    }

    /**
     * @depends testVerify
     */
    public function testParseResultProduct($result)
    {
        $this->assertEquals(
            new Struct\SearchResult\Product(
                array(
                    'title' => 'Givenchy Pour Homme Eau de Toilette Spray 100ml',
                    'shortDescription' => '<p>Givenchy Pour Homme Eau de Toilette Spray 100ml</p>',
                    'longDescription' => '<p>Givenchy Pour Homme Eau de Toilette Spray 100mlNew</p>',
                    'categories' => null,
                    'priceFrom' => 42.29,
                    'priceTo' => 42.29,
                    'currency' => 'EUR',
                    'availability' => 0,
                    'language' => 'de_DE',
                    'vendor' => 'Givenchy',
                    'url' => null,
                )
            ),
            $result->results[0]
        );
    }

    public function testVerifyAgainstRealService()
    {
        return;
        $searchService = new Search(
            new HttpClient\Stream('http://search.bepado.local/')
        );

        $result = $searchService->search(
            new Struct\Search(
                array(
                    'query' => 'homme',
                )
            )
        );

        $this->assertTrue($result instanceof Struct\SearchResult);
    }
}
