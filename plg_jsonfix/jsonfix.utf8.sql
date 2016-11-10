UPDATE #__users SET params = REPLACE(params,'&quot;','"');
UPDATE #__categories SET params = REPLACE(params,'&quot;','"');
UPDATE #__categories SET metadata= REPLACE(metadata,'&quot;','"');
UPDATE #__content SET images = REPLACE(images,'&quot;','"');
UPDATE #__content SET urls = REPLACE(urls,'&quot;','"');
UPDATE #__content SET attribs = REPLACE(attribs,'&quot;','"');