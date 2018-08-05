<?php

namespace OOReq\Header;


class HeaderlistTest extends \PHPUnit\Framework\TestCase
{

	/**
	 *
	 */
	public function test_addheader()
	{
		$HL = new Headerlist(new ContentLength(100), new ContentType('text/html'));

		$newHL = $HL->addHeader(new Header('myHeader', 'value'));
		$this->assertCount(2, $HL->asArray(), 'Headerlist should be immutable');
		$this->assertCount(3, $newHL->asArray());


		$this->assertTrue($newHL->containsHeader(new Header('myHeader')));
		$Header = $newHL->get(new Header('myHeader'));

		$this->assertEquals('myHeader', $Header->name());
		$this->assertEquals('value', $Header->value());
	}

	/**
	 * @throws NotFoundException
	 */
	public function testRemoveheader()
	{
		$HL    = new Headerlist(new ContentLength(100), new ContentType('text/html'));
		$newHL = $HL->removeHeader(new ContentLength());

		$this->assertCount(1, $newHL);
		$this->assertFalse($newHL->containsHeader(new ContentLength()));
	}

	/**
	 *
	 */
	public function testIterate()
	{
		$HL = new Headerlist(
			new ContentLength(100),
			new ContentType('text/html'),
			new Header('abc', 'valueA'),
			new Header('abc', 'valueB')
		);

		$header = [];
		foreach ($HL as $Header)
		{
			$header[] = $Header;
		}
		$this->assertInstanceOf(ContentLength::class, $header[0]);
		$this->assertInstanceOf(ContentType::class, $header[1]);
		$this->assertInstanceOf(Header::class, $header[2]);
		$this->assertInstanceOf(Header::class, $header[3]);
	}

	/**
	 * If there are multiple Headers with the same name, they will be
	 * joined.
	 * @throws NotFoundException
	 */
	public function testGetJoinedHeader()
	{
		$HL = new Headerlist(
			new ContentLength(100),
			new ContentType('text/html'),
			new Header('abc', 'valueA'),
			new Header('abc', 'valueB')
		);

		$this->assertCount(4, $HL);

		$Header = $HL->get(new Header('abc'));
		$this->assertEquals('valueA,valueB', $Header->value());
	}

	/**
	 * @throws NotFoundException
	 */
	public function testRemoveJoinedHeader()
	{
		$HL            = new Headerlist(
			new ContentLength(100),
			new ContentType('text/html'),
			new Header('abc', 'valueA'),
			new Header('abc', 'valueB')
		);
		$Newheaderlist = $HL->removeHeader(new Header('abc'));
		$this->assertCount(2, $Newheaderlist);
	}

	/**
	 * @throws NotFoundException
	 */
	public function testJoinedHeader()
	{
		$HL     = new Headerlist(
			new ContentLength(100),
			new ContentType('text/html'),
			new Header('abc', 'valueA'),
			new Header('abc', 'valueB')
		);
		$Header = $HL->get(new Header('abc'));
		$this->assertTrue($Header->wasJoined());
	}
}
