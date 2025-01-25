# Font Preload Performance Test

Start the Server:

    docker compose up -d

Visit [localhost:8088](http://localhost:8088) in your browser.

Generate test URL combinations:

    docker compose exec web php generate.php http://localhost:8088 > font-preload-urls.txt


Run the Web Vitals tests using [WPP Research](https://github.com/GoogleChromeLabs/wpp-research):

    git clone https://github.com/GoogleChromeLabs/wpp-research.git
    cd wpp-research
    nvm use
    npm install
    npm run research benchmark-web-vitals -- --file=../font-preload-urls.txt -n 2  -w "360x640"
