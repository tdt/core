var a=null;
PR.registerLangHandler(PR.createSimpleLexer([["pun",/^[:>?|]+/,a,":|>?"],["dec",/^%(?:YAML|TAG)r#]+/,a,"%"],["typ",/^&S+/,a,"&"],["typ",/^!S*/,a,"!"],["str",/^"(?:[^"]|.)*(?:"|$)/,a,'"'],["str",/^'(?:[^']|'')*(?:'|$)/,a,"'"],["com",/^#r]*/,a,"#"],["pln",/s+/,a," n"]],[["dec",/^(?:---|...)(?:r]|$)/],["pun",/^-/],["kwd",/w+:r ]/],["pln",/w+/]]),["yaml","yml"]);
