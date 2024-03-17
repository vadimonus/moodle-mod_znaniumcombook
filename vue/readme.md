To download prerequisites:
    
    npm install
    docker run -it --rm --volume=$(pwd):/app node:lts-alpine npm --prefix=/app/vue install

To build debug version:

    npm run dev
    docker run -it --rm --volume=$(pwd):/app node:lts-alpine npm --prefix=/app/vue run dev
    
To build production version:

    npm run prod
    docker run -it --rm --volume=$(pwd):/app node:lts-alpine npm --prefix=/app/vue run prod
    
