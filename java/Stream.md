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


