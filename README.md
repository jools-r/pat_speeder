# pat-speeder ![license](https://img.shields.io/github/license/cara-tm/pat_speeder.svg?maxAge=3600) ![version](https://img.shields.io/github/tag/cara-tm/pat_speeder.svg) ![Textpattern 4.7+](https://img.shields.io/badge/Textpattern-4.7%2B-brightgreen.svg?maxAge=3600)

A Textpattern CMS plugin. ![Last Commit](https://img.shields.io/github/last-commit/cara-tm/pat_speeder.svg)

Did you ever seen the source code of the Google main page? For speed reasons, Google serves its home page into one line of code. The benefit is a server bandwidth gain. So what don't we make the same for ours TXP websites?

Just activate this plugin and your page templates will be rendered into one line of code:

    <txp:pat_speeder enable="1" gzip="1" /><!DOCTYPE ...

See the plugin help for attributes details.

**Warning**: This plugin seems not to be compatible with some flash audio players (to be confirmed).

You can take a benefit rendition between 5% (for precompressed pages) and 6% (normal pages) according to [Ruud van Melick](https://vanmelick.com/)'s observations.

Included automatic server side GZIP compression, if available, gives an average additional benefit of 75%.

This plugin (v 0.7) seems to get **better results** than ask_header (v 0.3.6) **up to 0.7%** (based on a vanilla default TXP installation).




