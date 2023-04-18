DELETE FROM `{prefix}settings` where `name` in (
    'autor_text',
    'cache_type',
    'date_format',
    'meta_keywords',
    'referrer_policy',
    'x_xss_protection',
);