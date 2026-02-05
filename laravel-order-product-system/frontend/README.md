# React Frontend

Modern React SPA for Product and Order Management.

## Features

- 📊 **Dashboard** - Overview of products and orders
- 📦 **Products Management** - CRUD operations for products
- 🛒 **Orders Management** - Create, view, update status, cancel orders

## Tech Stack

- React 18
- Vite
- React Router DOM
- Axios
- Tailwind CSS

## Getting Started

### Install Dependencies

```bash
cd frontend
npm install
```

### Run Development Server

```bash
npm run dev
```

The app will be available at `http://localhost:5173`

### Build for Production

```bash
npm run build
```

## API Configuration

Update the API URL in `src/services/api.js`:

```javascript
const API_URL = 'http://localhost:8000/api';
```

## Project Structure

```
frontend/
├── src/
│   ├── components/     # Reusable components
│   ├── pages/          # Page components
│   ├── services/       # API services
│   ├── context/        # React context (future)
│   ├── App.jsx        # Main app with routing
│   └── main.jsx       # Entry point
├── tailwind.config.js  # Tailwind configuration
└── vite.config.js     # Vite configuration
```

## Available Pages

| Route | Description |
|-------|-------------|
| `/` | Dashboard with stats |
| `/products` | Product management |
| `/orders` | Order management |

## Screenshots

### Dashboard
- Total products count
- Total orders count
- Pending orders
- Completed orders
- Recent orders list

### Products Page
- Product list with search
- Add/Edit product modal
- Delete product

### Orders Page
- Order list with status filter
- Create new order with items
- View order details
- Update order status
- Cancel order
