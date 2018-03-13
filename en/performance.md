
## Performance

### Obullo router package (with Zend Diactoros)

100 route test, match with latest route (worst case)

```
ab -n 1000 -c 100 http://router/dummy/index/850/test
```

```
Server Software:        Apache/2.4.27
Server Hostname:        router
Server Port:            80

Document Path:          /dummy/index/850/test
Document Length:        22 bytes

Concurrency Level:      100
Time taken for tests:   2.680 seconds
Complete requests:      1000
Failed requests:        0
Total transferred:      189000 bytes
HTML transferred:       22000 bytes
Requests per second:    373.09 [#/sec] (mean)
Time per request:       268.028 [ms] (mean)
Time per request:       2.680 [ms] (mean, across all concurrent requests)
Transfer rate:          68.86 [Kbytes/sec] received

Connection Times (ms)
              min  mean[+/-sd] median   max
Connect:        0    2   6.3      0      26
Processing:    15  255  34.4    260     356
Waiting:        8  249  36.0    254     348
Total:         30  258  31.8    261     357

Percentage of the requests served within a certain time (ms)
  50%    261
  66%    267
  75%    270
  80%    272
  90%    280
  95%    294
  98%    324
  99%    344
 100%    357 (longest request)
```

### Other route packages focused on speed (with Zend Diactoros)

100 route test, match with latest route (worst case)

```
ab -n 1000 -c 100 http://router/dummy/index/850/test
```

```
Server Software:        Apache/2.4.27
Server Hostname:        router
Server Port:            80

Document Path:          /dummy/index/850/test
Document Length:        37 bytes

Concurrency Level:      100
Time taken for tests:   2.455 seconds
Complete requests:      1000
Failed requests:        0
Total transferred:      204000 bytes
HTML transferred:       37000 bytes
Requests per second:    407.41 [#/sec] (mean)
Time per request:       245.455 [ms] (mean)
Time per request:       2.455 [ms] (mean, across all concurrent requests)
Transfer rate:          81.16 [Kbytes/sec] received

Connection Times (ms)
              min  mean[+/-sd] median   max
Connect:        0    1   4.1      0      16
Processing:    13  233  41.7    242     365
Waiting:        8  228  40.9    236     362
Total:         22  234  38.7    242     365

Percentage of the requests served within a certain time (ms)
  50%    242
  66%    248
  75%    252
  80%    255
  90%    261
  95%    266
  98%    275
  99%    287
 100%    365 (longest request)
```

## Conclusions

As you can see, the performance of the Obullo router package is almost identical to other route packages focused on speed. The Obullo router package is designed to simplify the implementation and to achieve high performance using minimal resources. Obullo solves the optional route problem willingly by the developer to retyping it in the route file.

The performance of an application first begins in the human mind. When designing your application for more performance, separate the Pipe object and the routes into groups. Take care that the maximum number of routes for each set of routes is 50 for worst case.