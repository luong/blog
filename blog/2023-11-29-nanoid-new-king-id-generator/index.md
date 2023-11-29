---
slug: nanoid-new-king-id-generator
title: NanoID - A New King of ID Generator
authors: [luong]
tags: [id-generator,algorithrm]
keywords: [12g]
draft: false
unlisted: false
hide_table_of_contents: true
---

[NanoID](https://github.com/ai/nanoid) is a lib for generating random identifiers. It is compared to [UUID](https://www.npmjs.com/package/uuid) but said faster, smaller in code size, friendlier in URL, and shorter in length (21 symbols vs 36) although both have the same collision probability. Also NanoId is widely ported to over 20 programming langiages.

For instance, with speed of generating 1000 IDs per hours, it needs ~149 billion years or 1,307,660T IDs in order to have a 1% probability of at least one collision.

## Install

```bash
npm install nanoid
```

## Usage

```js
import { nanoid } from 'nanoid';
const id = nanoid(); // "V1StGXR8_Z5jdHi6B-myT"
```

In case you want to customize alphabet or ID length:
```js
import { customAlphabet } from 'nanoid';
const nanoid = customAlphabet('1234567890abcdef', 10);
const id = nanoid() // "4f90d13a42"
```

## References
* [NanoID](https://github.com/ai/nanoid) The offical github of NanoID project.
* [NanoID Collision Calculator](https://zelark.github.io/nano-id-cc) shows collision probability in visuals.


