### allMatch(Predicate<? super T> predicate):boolean
Kiểm tra tất cả các item có thoả điều kiện không
```java
boolean exists = Arrays.asList("a1", "a2", "a3")
						      .stream()
						      .allMatch(e -> e.startsWith("a"));
```                  
### anyMatch(Predicate<? super T> predicate):boolean
Có bất kỳ item nào thoả điều kiện không
```java
boolean exists = Arrays.asList("a1", "a2", "a3")
						    .stream()
						    .anyMatch(e -> e.endsWith("2"));
```
### noneMatch(Predicate<? super T> predicate):boolean
Không có phần tử nào thoả điều kiện 
```java
boolean exists = Arrays.asList("a1", "a2", "a3")
						    .stream()
						    .anyMatch(e -> e.endsWith("2"));
```

### collect(Collector<? super T,A,R> collector):<R,A> R
Dùng xử lý tính toán, ví dụ SUM
Sử dụng chung với java.util.stream.Collectors (Nghiên cứu thêm)

```java
List<Integer> list = Arrays.asList(3,5,6);
int sum = list.stream().collect(Collectors.summingInt(i->i));
```

### concat(Stream<? extends T> a, Stream<? extends T> b): static <T> Stream<T>
Join list lại với nhau
```java
Stream<Integer> resStream = Stream.concat(Arrays.asList(1,2,3).stream(), Arrays.asList(3,5,6).stream());
        resStream.forEach(s->System.out.print(s+" "));
```

### count() : long
Đếm số lượng phần tử
```java
long cnt = Arrays.asList(1,2,3).stream().count();
```




http://docs.oracle.com/javase/8/docs/api/java/util/stream/Stream.html 
http://www.concretepage.com/java/jdk-8/java-8-stream-tutorial-with-example
