UPDATE healtyv2_users SET params = REPLACE(params,'&quot;','"');
UPDATE healtyv2_categories SET params = REPLACE(params,'&quot;','"');
UPDATE healtyv2_categories SET rules = REPLACE(rules,'&quot;','"');
UPDATE healtyv2_categories SET metadata= REPLACE(metadata,'&quot;','"');
UPDATE healtyv2_content SET images = REPLACE(images,'&quot;','"');
UPDATE healtyv2_content SET urls = REPLACE(urls,'&quot;','"');
UPDATE healtyv2_content SET attribs = REPLACE(attribs,'&quot;','"');