# Testing
This extension ships with Integration Tests (`Test/Integration/`) and a Travis CI build (`.travis.yml` which triggers scripts in `.ci-scripts/`). The CI scripts can be tested locally with the `Dockerfile`:

    docker build -t ci .

docker-compose up --build --abort-on-container-exit

