<?php

namespace OOReq;


final class FileAsPOST extends AbstractData
{
	public function __construct(?string $key = null, ?\SplFileObject $File = null, ?MIMEType $MIMEType = null)
	{
		if ($key == null || $File == null)
		{
			return;
		}

		if (is_null($MIMEType))
		{
			$MIMEType = new MIMEType();
		}
		$File = new \CURLFile($File->getPathname(), $MIMEType->asString(), $File->getFilename());
		parent::__construct($key, $File);
	}
}