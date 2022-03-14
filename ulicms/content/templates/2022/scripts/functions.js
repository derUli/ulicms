const detectRobot = (userAgent) => {
    const robots = new RegExp([
        /bot/, /spider/, /crawl/, // GENERAL TERMS
        /APIs-Google/, /AdsBot/, /Googlebot/, // GOOGLE ROBOTS
        /mediapartners/, /Google Favicon/,
        /FeedFetcher/, /Google-Read-Aloud/,
        /DuplexWeb-Google/, /googleweblight/,
        /bing/, /yandex/, /baidu/, /duckduck/, /yahoo/, // OTHER ENGINES
        /ecosia/, /ia_archiver/,
        /facebook/, /instagram/, /pinterest/, /reddit/, // SOCIAL MEDIA
        /slack/, /twitter/, /whatsapp/, /youtube/,
        /semrush/, // OTHER
    ].map((r) => r.source).join("|"), "i");               // BUILD REGEXP + "i" FLAG

    return robots.test(userAgent);
};

/**
 * Check if an element is truncated.
 */
const isTruncated = (el) => {
    return el.scrollWidth > el.clientWidth
}