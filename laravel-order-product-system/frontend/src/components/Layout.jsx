import { Link, useLocation, Outlet } from 'react-router-dom';
import { useState } from 'react';

const navItems = [
  { path: '/', label: 'Dashboard', icon: '📊' },
  { path: '/products', label: 'Products', icon: '📦' },
  { path: '/orders', label: 'Orders', icon: '🛒' },
  { path: '/customers', label: 'Customers', icon: '👥' },
];

const Layout = () => {
  const location = useLocation();
  const [sidebarOpen, setSidebarOpen] = useState(false);

  return (
    <div className="min-vh-100 bg-light">
      {/* Mobile sidebar overlay */}
      {sidebarOpen && (
        <div 
          className="position-fixed top-0 start-0 w-100 h-100 bg-dark opacity-50 d-lg-none"
          onClick={() => setSidebarOpen(false)}
          style={{ zIndex: 1040 }}
        />
      )}

      {/* Sidebar */}
      <aside className={`position-fixed top-0 start-0 h-100 bg-dark text-white shadow-lg z-3 transition-width transition-duration-300 d-flex flex-column ${sidebarOpen ? 'w-100' : ''}`} style={{ width: '280px', zIndex: 1050 }}>
        {/* Logo */}
        <div className="d-flex align-items-center justify-content-between px-4 py-3 bg-gradient bg-primary">
          <Link to="/" className="d-flex align-items-center text-white text-decoration-none">
            <div className="bg-white bg-opacity-25 rounded-3 px-2 py-1 me-2">
              <span className="fw-bold fs-5">S</span>
            </div>
            <span className="fw-semibold fs-5">ShopAdmin</span>
          </Link>
          <button 
            className="btn btn-link btn-sm text-white d-lg-none p-0"
            onClick={() => setSidebarOpen(false)}
          >
            ✕
          </button>
        </div>

        {/* Navigation */}
        <nav className="flex-grow-1 py-3">
          <ul className="list-unstyled mb-0 px-3">
            {navItems.map((item) => {
              const isActive = location.pathname === item.path;
              return (
                <li key={item.path} className="mb-1">
                  <Link
                    to={item.path}
                    className={`d-flex align-items-center px-3 py-2 rounded-3 text-decoration-none transition-colors ${isActive 
                      ? 'bg-primary text-white shadow-lg' 
                      : 'text-light-50 hover-bg-dark'
                    }`}
                    onClick={() => setSidebarOpen(false)}
                  >
                    <span className="fs-5 me-3">{item.icon}</span>
                    <span className="fw-medium">{item.label}</span>
                  </Link>
                </li>
              );
            })}
          </ul>
        </nav>

        {/* User info */}
        <div className="p-3 border-top border-secondary">
          <div className="d-flex align-items-center">
            <div className="bg-success bg-opacity-25 rounded-circle p-2 me-3">
              <span className="text-white fw-semibold">A</span>
            </div>
            <div>
              <p className="mb-0 text-white fw-medium small">Admin User</p>
              <p className="mb-0 text-light small">admin@shop.com</p>
            </div>
          </div>
        </div>
      </aside>

      {/* Main content */}
      <div className="ps-lg-5" style={{ marginLeft: '280px' }}>
        {/* Top bar */}
        <header className="sticky-top bg-white shadow-sm z-2">
          <div className="d-flex align-items-center justify-content-between px-4 py-3">
            <button 
              className="btn btn-outline-secondary d-lg-none"
              onClick={() => setSidebarOpen(true)}
            >
              ☰
            </button>
            
            <div className="text-muted small">
              {new Date().toLocaleDateString('en-US', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' })}
            </div>
          </div>
        </header>

        {/* Page content */}
        <main className="p-4">
          <Outlet />
        </main>
      </div>
    </div>
  );
};

export default Layout;
