<?php
// Sende HTTP Status 503 und Retry-After im Wartungsmodus
header('HTTP/1.0 503 Service Temporarily Unavailable');
header('Status: 503 Service Temporarily Unavailable');
header('Retry-After: 60');
