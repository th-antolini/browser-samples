# 📝 Todo Manager

A modern, responsive todo list management application built with Laravel and Vue.js. This application provides a clean and intuitive interface for managing your tasks across both mobile and desktop devices.

## ✨ Features

- **Modern UI/UX**: Beautiful, responsive design that works seamlessly on mobile and desktop
- **Task Management**: Create, edit, delete, and organize your tasks
- **Priority Levels**: Set priorities (Low, Medium, High) for better task organization
- **Due Dates**: Set due dates with date/time picker
- **Status Tracking**: Mark tasks as completed or pending
- **Real-time Search**: Search through your tasks instantly
- **Smart Filtering**: Filter tasks by status (All, Pending, Completed)
- **Statistics Dashboard**: View task statistics at a glance
- **Responsive Design**: Optimized for all screen sizes

## 🛠 Tech Stack

- **Backend**: Laravel 12 (PHP 8.2+)
- **Frontend**: Vue.js 3 with Composition API
- **Styling**: Tailwind CSS
- **Database**: SQLite (default) - easily configurable to MySQL/PostgreSQL
- **Build Tool**: Vite
- **API**: RESTful API with JSON responses

## 📋 Requirements

- PHP 8.2 or higher
- Composer
- Node.js 18 or higher
- npm or yarn

## 🚀 Installation

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd todo-manager
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Install Node.js dependencies**
   ```bash
   npm install
   ```

4. **Environment setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. **Database setup**
   ```bash
   php artisan migrate
   ```

6. **Build frontend assets**
   ```bash
   npm run build
   ```

## 🏃‍♂️ Running the Application

### Development Mode

1. **Start the Laravel development server**
   ```bash
   php artisan serve
   ```

2. **In another terminal, start the Vite development server**
   ```bash
   npm run dev
   ```

3. **Access the application**
   Open your browser and visit `http://localhost:8000`

### Production Mode

1. **Build production assets**
   ```bash
   npm run build
   ```

2. **Serve with a web server**
   Configure your web server (Apache/Nginx) to serve from the `public` directory

## 📱 API Endpoints

The application provides a RESTful API for managing todos:

- `GET /api/todos` - Get all todos (with optional filters)
- `POST /api/todos` - Create a new todo
- `GET /api/todos/{id}` - Get a specific todo
- `PUT /api/todos/{id}` - Update a todo
- `DELETE /api/todos/{id}` - Delete a todo
- `PATCH /api/todos/{id}/toggle` - Toggle todo completion status

### API Query Parameters

- `status`: Filter by status (`pending`, `completed`)
- `priority`: Filter by priority (`low`, `medium`, `high`)
- `search`: Search in title and description

## 🎨 Features Overview

### Dashboard Statistics
- Total tasks count
- Completed tasks count
- Pending tasks count
- High priority tasks count

### Task Management
- **Create**: Add new tasks with title, description, priority, and due date
- **Edit**: Inline editing of existing tasks
- **Delete**: Remove tasks with confirmation
- **Toggle**: Quick completion status toggle
- **Search**: Real-time search functionality
- **Filter**: Filter by completion status

### Responsive Design
- **Mobile-first**: Optimized for mobile devices
- **Desktop-friendly**: Enhanced experience on larger screens
- **Touch-friendly**: Large tap targets for mobile interaction
- **Accessible**: Proper semantic markup and keyboard navigation

## 🗄 Database Schema

### Todos Table
```sql
- id (bigint, primary key)
- title (string, required)
- description (text, nullable)
- completed (boolean, default: false)
- priority (enum: low|medium|high, default: medium)
- due_date (timestamp, nullable)
- created_at (timestamp)
- updated_at (timestamp)
```

## 🧪 Testing

Run the test suite:
```bash
php artisan test
```

## 🔧 Configuration

### Database Configuration
Edit the `.env` file to configure your database:

```env
DB_CONNECTION=sqlite
DB_DATABASE=/absolute/path/to/database.sqlite
```

For MySQL/PostgreSQL:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=todo_manager
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### Asset Building
- Development: `npm run dev`
- Production: `npm run build`
- Watch mode: `npm run dev` (with hot reload)

## 🚀 Deployment

### Production Checklist
1. Set `APP_ENV=production` in `.env`
2. Set `APP_DEBUG=false` in `.env`
3. Run `composer install --optimize-autoloader --no-dev`
4. Run `npm run build`
5. Run `php artisan config:cache`
6. Run `php artisan route:cache`
7. Set up proper web server configuration

### Web Server Configuration
Point your web server document root to the `public` directory and ensure URL rewriting is enabled.

## 🎯 Browser Support

- Chrome 90+
- Firefox 88+
- Safari 14+
- Edge 90+

## 📄 License

This project is open-sourced software licensed under the [MIT license](LICENSE).

## 🤝 Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add some amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## 📞 Support

If you encounter any issues or have questions, please file an issue on the GitHub repository.

---

**Built with ❤️ using Laravel and Vue.js**
