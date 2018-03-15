update `{prefix}settings` set value = REPLACE(value, '||', '\n') where name = 'spamfilter_words_blacklist';
