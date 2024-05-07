import pika

# Establish connection with RabbitMQ server
credentials = pika.PlainCredentials('test', 'test')  # RabbitMQ credentials
parameters = pika.ConnectionParameters('localhost', 5672, 'testHost', credentials)  # ip address, port, virtual host
connection = pika.BlockingConnection(parameters)
channel = connection.channel()

# Declare exchange with type 'topic' and durable=True
channel.exchange_declare(exchange='login_events', exchange_type='topic', durable=True)

# Declare queue for successful logins
channel.queue_declare(queue='successful_logins', durable=True)

# Publish messages only for successful logins queue
def publish_login_event(event_type, message):
    channel.basic_publish(exchange='login_events', routing_key=f'login.success.{event_type}', body=message)

# Send message to the queue
publish_login_event('info', 'Successful login attempt.')

# Close connection
connection.close()

