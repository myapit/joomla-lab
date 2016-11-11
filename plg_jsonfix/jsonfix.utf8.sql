UPDATE #__users SET params = REPLACE(params,'&quot;','"');

UPDATE #__categories SET params = REPLACE(params,'&quot;','"');
UPDATE #__categories SET metadata= REPLACE(metadata,'&quot;','"');

UPDATE #__content SET images = REPLACE(images,'&quot;','"');
UPDATE #__content SET urls = REPLACE(urls,'&quot;','"');
UPDATE #__content SET attribs = REPLACE(attribs,'&quot;','"');
UPDATE #__content SET metadata = REPLACE(metadata,'&quot;','"');


UPDATE #__users SET params = REPLACE(params,'&amp;quot;','"');

UPDATE #__categories SET params = REPLACE(params,'&amp;quot;','"');
UPDATE #__categories SET metadata= REPLACE(metadata,'&amp;quot;','"');

UPDATE #__content SET images = REPLACE(images,'&amp;quot;','"');
UPDATE #__content SET urls = REPLACE(urls,'&amp;quot;','"');
UPDATE #__content SET attribs = REPLACE(attribs,'&amp;quot;','"');
UPDATE #__content SET metadata = REPLACE(metadata,'&amp;quot;','"');
