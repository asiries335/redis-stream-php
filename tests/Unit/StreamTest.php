<?php


namespace Asiries335\redisSteamPhp\Tests;

use Asiries335\redisSteamPhp\Data\Collection;
use Asiries335\redisSteamPhp\Stream;
use PHPUnit\Framework\TestCase;

class StreamTest extends TestCase
{
    private $client;

    private const TEST_NAME_STREAM = 'test_stream';

    /**
     * setUp
     *
     * @return void
     */
    public function setUp() : void
    {
        $this->client = \Mockery::mock(\Redis::class);
    }

    /**
     * Read empty stream
     *
     * @throws \Exception
     *
     * @return void
     */
    public function testReadEmptyStream() : void
    {
        $this->client->shouldReceive('rawCommand')->andReturn([]);

        $stream = new Stream($this->client, self::TEST_NAME_STREAM);

        $collection = $stream->get();

        $this->assertEquals(new Collection, $collection);
    }

    /**
     * Add data to stream
     *
     * @throws \Exception
     *
     * @return void
     */
    public function testAddDataToStream() : void
    {
        $key = 'name';

        $values = [
            'id'   => 123,
            'name' => 'Barney',
            'age'  => 25,
        ];

        $this->client->shouldReceive('rawCommand')->andReturn($key);

        $this->stream = new Stream($this->client, self::TEST_NAME_STREAM);

        $result = $this->stream->add($key, $values);

        $this->assertEquals($key, $result);
    }

    /**
     * Read empty stream
     *
     * @throws \Exception
     *
     * @return void
     */
    public function testReadStream() : void
    {
        $data = [
            [
                'name',
                [
                    [
                        'id',
                        'key',
                        'body'
                    ]
                ]
            ]
        ];

        $this->client->shouldReceive('rawCommand')->andReturn($data);

        $stream = new Stream($this->client, self::TEST_NAME_STREAM);

        $collectionStream = $stream->get();

        $this->assertEquals(Collection::create($data), $collectionStream);

    }
}