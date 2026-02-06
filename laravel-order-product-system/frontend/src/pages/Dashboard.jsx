import { useState, useEffect } from 'react';
import { Link } from 'react-router-dom';
import { productsAPI, ordersAPI } from '../services/api';

const StatCard = ({ title, value, icon, color, trend }) => (
  <div className="card h-100 border-0 shadow-sm hover-shadow">
    <div className="card-body d-flex align-items-start justify-content-between">
      <div>
        <p className="text-muted small mb-1">{title}</p>
        <h2 className="mb-0 fw-bold">{value}</h2>
        {trend !== undefined && (
          <p className={`small mb-0 ${trend > 0 ? 'text-success' : 'text-danger'}`}>
            {trend > 0 ? '↑' : '↓'} {Math.abs(trend)}% from last month
          </p>
        )}
      </div>
      <div className={`rounded-3 p-3 ${color}`}>
        <span className="fs-4">{icon}</span>
      </div>
    </div>
  </div>
);

const QuickAction = ({ title, description, link, buttonText, icon, color }) => (
  <div className="card border-0 shadow-sm hover-shadow transition-all">
    <div className="card-body d-flex align-items-start">
      <div className={`rounded-3 p-3 me-3 ${color} transition-transform`}>
        <span className="fs-4">{icon}</span>
      </div>
      <div className="flex-grow-1">
        <h5 className="card-title mb-1">{title}</h5>
        <p className="text-muted small mb-3">{description}</p>
        <Link
          to={link}
          className="btn btn-primary btn-sm stretched-link"
        >
          {buttonText} →
        </Link>
      </div>
    </div>
  </div>
);

const StatusBadge = ({ status }) => {
  const styles = {
    pending: 'bg-warning text-dark',
    processing: 'bg-info text-white',
    completed: 'bg-success text-white',
    cancelled: 'bg-danger text-white',
    refunded: 'bg-secondary text-white',
  };

  const labels = {
    pending: 'Pending',
    processing: 'Processing',
    completed: 'Completed',
    cancelled: 'Cancelled',
    refunded: 'Refunded',
  };

  return (
    <span className={`badge ${styles[status] || 'bg-secondary'}`}>
      {labels[status] || status?.charAt(0).toUpperCase() + status?.slice(1) || 'Unknown'}
    </span>
  );
};

const Dashboard = () => {
  const [stats, setStats] = useState({
    totalProducts: 0,
    totalOrders: 0,
    pendingOrders: 0,
    completedOrders: 0,
  });
  const [recentOrders, setRecentOrders] = useState([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    fetchDashboardData();
  }, []);

  const fetchDashboardData = async () => {
    try {
      const [productsRes, ordersRes] = await Promise.all([
        productsAPI.getAll(),
        ordersAPI.getAll(),
      ]);

      const products = productsRes.data.data || [];
      const orders = ordersRes.data.data || [];

      setStats({
        totalProducts: products.length,
        totalOrders: orders.length,
        pendingOrders: orders.filter(o => o.attributes?.status?.value === 'pending').length,
        completedOrders: orders.filter(o => o.attributes?.status?.value === 'completed').length,
      });

      setRecentOrders(orders.slice(0, 5));
    } catch (error) {
      console.error('Error fetching dashboard data:', error);
    } finally {
      setLoading(false);
    }
  };

  if (loading) {
    return (
      <div className="d-flex align-items-center justify-content-center vh-50">
        <div className="text-center">
          <div className="spinner-border text-primary mb-3" role="status">
            <span className="visually-hidden">Loading...</span>
          </div>
          <p className="text-muted">Loading...</p>
        </div>
      </div>
    );
  }

  return (
    <div className="container-fluid px-0">
      {/* Page header */}
      <div className="d-flex align-items-center justify-content-between mb-4">
        <div>
          <h1 className="h3 mb-1">Dashboard</h1>
          <p className="text-muted mb-0">Welcome back! Here's your store overview.</p>
        </div>
      </div>

      {/* Stats Grid */}
      <div className="row g-4 mb-4">
        <div className="col-md-6 col-xl-3">
          <StatCard
            title="Total Products"
            value={stats.totalProducts}
            icon="📦"
            color="bg-primary bg-opacity-10 text-primary"
            trend={12}
          />
        </div>
        <div className="col-md-6 col-xl-3">
          <StatCard
            title="Total Orders"
            value={stats.totalOrders}
            icon="🛒"
            color="bg-purple bg-opacity-10 text-purple"
            trend={8}
          />
        </div>
        <div className="col-md-6 col-xl-3">
          <StatCard
            title="Pending Orders"
            value={stats.pendingOrders}
            icon="⏳"
            color="bg-warning bg-opacity-10 text-warning"
          />
        </div>
        <div className="col-md-6 col-xl-3">
          <StatCard
            title="Completed"
            value={stats.completedOrders}
            icon="✅"
            color="bg-success bg-opacity-10 text-success"
          />
        </div>
      </div>

      {/* Quick Actions */}
      <div className="row g-4 mb-4">
        <div className="col-md-6">
          <QuickAction
            title="Manage Products"
            description="Add, edit, or delete products in your catalog"
            link="/products"
            buttonText="Go to Products"
            icon="📦"
            color="bg-primary bg-opacity-10 text-primary"
          />
        </div>
        <div className="col-md-6">
          <QuickAction
            title="Manage Orders"
            description="View and process customer orders"
            link="/orders"
            buttonText="Go to Orders"
            icon="🛒"
            color="bg-purple bg-opacity-10 text-purple"
          />
        </div>
      </div>

      {/* Recent Orders */}
      {recentOrders.length > 0 && (
        <div className="card border-0 shadow-sm">
          <div className="card-header bg-white d-flex align-items-center justify-content-between py-3">
            <h5 className="mb-0">Recent Orders</h5>
            <Link to="/orders" className="btn btn-outline-primary btn-sm">
              View all →
            </Link>
          </div>
          <div className="card-body p-0">
            <div className="table-responsive">
              <table className="table table-hover mb-0">
                <thead className="table-light">
                  <tr>
                    <th className="ps-4">Order #</th>
                    <th>Items</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th className="pe-4">Date</th>
                  </tr>
                </thead>
                <tbody>
                  {recentOrders.map((order) => (
                    <tr key={order.id}>
                      <td className="ps-4 fw-medium">
                        #{order.attributes?.order_number || order.id}
                      </td>
                      <td>{order.attributes?.items_count || 0} items</td>
                      <td className="fw-medium">
                        {order.attributes?.total_amount?.formatted || 'N/A'}
                      </td>
                      <td>
                        <StatusBadge status={order.attributes?.status?.value} />
                      </td>
                      <td className="text-muted pe-4">
                        {order.meta?.created_at
                          ? new Date(order.meta.created_at).toLocaleDateString()
                          : 'N/A'}
                      </td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>
          </div>
        </div>
      )}
    </div>
  );
};

export default Dashboard;
