To download prerequisites:
    
    npm install
    docker run -it --rm --volume=$(pwd):/app node:lts-alpine npm --prefix=/app/scss install

To build production version:

    npm run prod
    docker run -it --rm --volume=$(pwd):/app node:lts-alpine npm --prefix=/app/vue run prod
    
