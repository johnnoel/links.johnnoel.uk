const axios = require('axios');
const { Readability } = require('@mozilla/readability');
const { JSDOM } = require('jsdom');

// fetch page contents
const link = process.argv[2];
axios.get(process.argv[2])
    .then(resp => {
        const doc = new JSDOM(resp.data, {
            url: link,
        });

        const reader = new Readability(doc.window.document);
        const article = reader.parse();

        console.log(JSON.stringify(article));
    })
    .catch(err => {
        console.error(err);
        process.exit(1);
    })
;
