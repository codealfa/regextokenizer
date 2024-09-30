# Regex Parsing
Regex Parsing offers a rudimentary solution for parsing strings representing some kind of grammar such as HTML, CSS, and JavaScript, using regular expressions (regex). 

One of the problems with using regex to find matches in a string like HTML is that there may be matches in certain contexts that are undesirable. For example, you may not want matches that occur in comments. There are ways around this, like matching the comments first then replacing them with placeholders so the string can be safely matched with regex. But if speed is a concern, these workarounds become very expensive as the string has to be matched, and replace functions called, multiple times to do the placeholder replacements. 

With Regex Parsing, one can 'parse' the string once, ignoring matches in unwanted contexts without spending unnecessary time and resources calling functions to manipulate placeholders. To do so, we must define regular expressions for each context and our match, ensuring that no two regex will match the same string, simultaneously match these regex using alternations, keeping the unwanted context out of the match. It is very important for the complete regex to consume the entire string without failing even once, so the end of the string is also matched, in the event the match or one of the contexts we're searching for does not occur at the end of the string.

Our Regex Parsing syntax takes this form:
```regexp
(?>[^ab]++|c|d|[ab])*?\K(?:m|$)
```
Where `ab` represents a character set of the first characters in each context and match, `c` and `d` would be regexes for all the other possible contexts in the string we're not interested in, and `m` the regex for the match we are interested in. You can then use your application logic to discard the zero-length match occurring at the end of the string.

Let's illustrate this with an example. Say we want to match an `<img>` tag in the following HTML, but we are not interested in the tag inside the HTML comment:

```html
<html>
<head>
    <title>Example</title>
</head>
<body>
 <!-- 
 <img src="image1.png" alt="unwanted image">
 -->
<img src="image2.png" alt="match image">
</body>
</html>
```

Our regex would then be:
```regexp
(?>[^<]++|<!--[^-]*?-->|<)*?\K(?:<img[^>]*?>|$)
```
You can test this in any online regex tester like [https://regex101.com/](https://regex101.com/), or if you have an awesome tool like [RegexBuddy](https://www.regexbuddy.com/) that you can use to see the number of steps the regex engine completed to find the matches.