# internal.py
from flask import Flask, jsonify
import pymysql
from pymysql.cursors import DictCursor
from config import DB_CONFIG  # ← import credentials from config.py

app = Flask(__name__)

@app.route('/api/user/<int:user_id>')
def get_user(user_id):
    conn = pymysql.connect(**DB_CONFIG)
    try:
        with conn.cursor(DictCursor) as cur:
            cur.execute(
                "SELECT id, name, username, email, bio, profile_pic, created_at "
                "FROM users WHERE id = %s",
                (user_id,)
            )
            user = cur.fetchone()
    finally:
        conn.close()

    if not user:
        return jsonify({'error': 'User not found'}), 404

    if user.get('created_at'):
        user['created_at'] = user['created_at'].strftime('%Y-%m-%d %H:%M:%S')

    return jsonify(user)

if __name__ == '__main__':
    app.run(host='127.0.0.1', port=5000)