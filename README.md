# PHP benchmark script

Benchmark your php code. The project is built on top of the original work from Alessandro Torrisi [www.php-benchmark-script.com](http://www.php-benchmark-script.com)

# examples

## does xdebug slow down code execution?

```bash
# disable xdebug extension in php.ini
$ php src/benchmark.php --iterations 1000 --time-per-iteration 50 --save xdebug_off

# enable xdebug extension
$ php src/benchmark.php --iterations 1000 --time-per-iteration 50 --save xdebug_on

# compare
$ php src/compare.php --file1 benchmark_xdebug_off_20201127-0946.txt --file2 benchmark_xdebug_on_20201127-0939.txt
------------------------------------------------
test_math
mean               :      3762      531   -85.9%
median             :      4226      568   -86.6%
mode               :      4655      596   -87.2%
minmum             :       918      188   -79.5%
maximum            :      4722      612   -87.0%
quartile 1         :      3081      490   -84.1%
quartile 3         :      4580      595   -87.0%
IQ range           :      1498      105   -93.0%
std deviation      :       984       87   -91.1%
normality          :     11.0%    11.0%
------------------------------------------------
...
```

## is php 8.0 faster than 7.4? it depends

```bash
$ docker run -it --volume "/$(pwd -W):/test/" php:7.4.12-cli-alpine sh
$ cd test
$ php src/benchmark.php --iterations 1000 --time-per-iteration 50 --save php7.4

$ docker run -it --volume "/$(pwd -W):/test/" php:8.0.0RC5-cli-alpine sh
$ cd test
$ php src/benchmark.php --iterations 1000 --time-per-iteration 50 --save php8.0

$ php src/compare.php --file1 benchmark_php7.4_20201127-0625.txt --file2 benchmark_php8_20201127-0617.txt
------------------------------------------------
test_if_else
mean               :    531059   520234    -2.0%
median             :    548296   537380    -2.0%
mode               :    561412   547744    -2.4%
minmum             :    258015   286786    11.2%
maximum            :    564597   552249    -2.2%
quartile 1         :    533671   512013    -4.1%
quartile 3         :    553146   544095    -1.6%
IQ range           :     19475    32082    64.7%
std deviation      :     41182    38131    -7.4%
normality          :      3.1%     3.1%
------------------------------------------------
test_loops
mean               :     16614    28723    72.9%
median             :     16912    29317    73.4%
mode               :     17074    29433    72.4%
minmum             :     11206    16460    46.9%
maximum            :     17305    29874    72.6%
quartile 1         :     16663    28671    72.1%
quartile 3         :     17030    29540    73.5%
IQ range           :       366      868   137.0%
std deviation      :       784     1513    92.9%
normality          :      1.8%     1.8%
------------------------------------------------
test_arrays
mean               :      6060     5985    -1.2%
median             :      6134     6153     0.3%
mode               :      6141     6218     1.3%
minmum             :      3939     3930    -0.2%
maximum            :      6231     6258     0.4%
quartile 1         :      6077     5913    -2.7%
quartile 3         :      6165     6203     0.6%
IQ range           :        88      290   229.5%
std deviation      :       217      354    63.3%
normality          :      1.2%     1.2%
------------------------------------------------
test_strings
mean               :     10543    10395    -1.4%
median             :     10743    10608    -1.3%
mode               :     10793    10662    -1.2%
minmum             :      6233     7403    18.8%
maximum            :     11047    10905    -1.3%
quartile 1         :     10563    10341    -2.1%
quartile 3         :     10844    10734    -1.0%
IQ range           :       281      393    39.9%
std deviation      :       591      568    -3.7%
normality          :      1.9%     1.9%
------------------------------------------------
test_math
mean               :      5479     6364    16.2%
median             :      5587     6513    16.6%
mode               :      5639     6603    17.1%
minmum             :      3851     3493    -9.3%
maximum            :      5739     6719    17.1%
quartile 1         :      5472     6333    15.7%
quartile 3         :      5645     6583    16.6%
IQ range           :       172      250    45.2%
std deviation      :       296      383    29.3%
normality          :      1.9%     1.9%
------------------------------------------------
test_hashes
mean               :      1456     1357    -6.8%
median             :      1485     1453    -2.2%
mode               :      1498     1544     3.1%
minmum             :       719      373   -48.1%
maximum            :      1518     1557     2.6%
quartile 1         :      1459     1259   -13.7%
quartile 3         :      1498     1525     1.8%
IQ range           :        39      266   583.3%
std deviation      :        80      218   172.1%
normality          :      1.9%     1.9%
------------------------------------------------
test_files
mean               :        27       27    -2.0%
median             :        28       28     0.0%
mode               :        27       29     7.4%
minmum             :        16        5   -68.8%
maximum            :        39       40     2.6%
quartile 1         :        26       25    -3.8%
quartile 3         :        30       30     0.0%
IQ range           :         4        5    25.0%
std deviation      :         3        4    55.0%
normality          :      1.3%     1.3%
------------------------------------------------
```

# definitions

> The mean is the same as the average value of a data set and is found using a calculation. Add up all of the numbers and divide by the number of numbers in the data set.

> The median is the central number of a data set. Arrange data points from smallest to largest and locate the central number. This is the median. If there are 2 numbers in the middle, the median is the average of those 2 numbers.

> The mode is the number in a data set that occurs most frequently. Count how many times each number occurs in the data set. The mode is the number with the highest tally. It's ok if there is more than one mode. And if all numbers occur the same number of times there is no mode.

> -- <cite>[calculatorsoup.com](https://www.calculatorsoup.com/calculators/statistics/mean-median-mode.php)</cite>

> The interquartile range (IQR) is the difference between the first quartile and third quartile. The formula for this is:

> IQR = Q3 - Q1

> There are many measurements of the variability of a set of data. Both the range and standard deviation tell us how spread out our data is. The problem with these descriptive statistics is that they are quite sensitive to outliers. A measurement of the spread of a dataset that is more resistant to the presence of outliers is the interquartile range.

> -- <cite>[thoughtco.com](https://www.thoughtco.com/what-is-the-interquartile-range-3126245)</cite>

> Outliers are values that lie above the Upper Fence or below the Lower Fence of the sample set.
> Upper Fence = Q3 + 1.5 × Interquartile Range
> Lower Fence = Q1 − 1.5 × Interquartile Range

> -- <cite>[calculatorsoup.com](https://www.calculatorsoup.com/calculators/statistics/mean-median-mode.php)</cite>

# interesting reads
https://kinsta.com/blog/php-benchmarks/
https://www.paulstephenborile.com/2018/03/code-benchmarks-can-measure-fast-software-make-faster/