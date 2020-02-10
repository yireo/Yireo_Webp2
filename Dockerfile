FROM php:7.3-cli
WORKDIR /source
COPY . .
RUN cd /source && .ci-scripts/pre-docker.sh
CMD ["ls", "/source"]
