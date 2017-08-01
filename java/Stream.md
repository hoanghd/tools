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

### distinct(): Stream<T>
Loại bỏ đi các item giống nhau
```java
long cnt = Arrays.asList(1,2,3,3,2,1).stream().distinct().count();
```

### empty(): static <T> Stream<T>
	
### filter(Predicate<? super T> predicate): Stream<T>
Lọc các giá trị thoả điểu kiện 
```java
Arrays.asList(1,2,3,4,5,6,7,8,9)
		.stream()
		.filter(num -> num>3);
```

### findAny(): Optional<T>
```java
String any = Arrays.asList("G","B","F","E")
		.stream()
		.findAny()
		.get();
```

### findFirst(): Optional<T>
Lấy phần tử đâu tiên
```java
String any = Arrays.asList("G","B","F","E")
		.stream()
		.findFirst()
		.get();
```

### flatMap(Function<? super T,? extends Stream<? extends R>> mapper): <R> Stream<R>
```java
Integer[][] data = {{1,2},{3,4},{5,6}};
		Arrays.stream(data)
			.flatMap(row -> Arrays.stream(row))
			.forEach(s -> System.out.print(s+" "));
= 1,2,3,4,5,6
```	
### flatMapToDouble(Function<? super T,? extends DoubleStream> mapper): DoubleStream
```java
Arrays.asList("1.2","2.2","3","4","5").stream()
	           .flatMapToDouble(n-> DoubleStream.of(Double.parseDouble(n)) )
	           .forEach(System.out::println);
```

### flatMapToInt(Function<? super T,? extends IntStream> mapper): IntStream
```java
Arrays.asList("1","2","3","4","5")
	.stream()
   	.mapToInt(n-> Integer.parseInt(n))
	.forEach(System.out::println);
```





	
http://docs.oracle.com/javase/8/docs/api/java/util/stream/Stream.html 
http://www.concretepage.com/java/jdk-8/java-8-stream-tutorial-with-example
http://www.java2s.com/Tutorials/Java_Streams/java.util.stream/Stream/Java_Streams_Stream.htm
