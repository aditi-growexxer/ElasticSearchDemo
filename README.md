# ElasticSearchDemo
until curl -s http://localhost:9200/ | grep -q '"tagline"'; do
  echo "Waiting for Elasticsearch to be ready..."
  sleep 3
done
echo "Elasticsearch is ready!"
curl http://localhost:9200/
