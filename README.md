** Time duration class

*** Usage

  ```
  use Duration
  $d = Duration::parse("1h59m59s999ms999us999ns");
  $d->add(Duration::parse("1ns");
  echo $d; // prints "2h"
  ```
