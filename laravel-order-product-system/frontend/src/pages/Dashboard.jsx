import { useState, useEffect } from 'react';
import { Link } from 'react-router-dom';
import { productsAPI, ordersAPI } from '../services/api';

const StatCard = ({ title, value, icon, color, trend }) => (
  <div className="bg-white rounded-xl shadow-sm border border-slate-200 p-6 hover:shadow-md transition-shadow duration-300">
    <div className="flex items-start justify-between">
      <div>
        <p className="text-sm font-medium text-slate-500">{title}</p>
        <p className="mt-2 text-3xl font-bold text-slate-900">{value}</p>
        {trend && (
          <p className={`mt-2 text-sm ${trend > 0 ? 'text-green-600' : 'text-red-600'}`}>
            {trend > 0 ? '↑' : '↓'} {Math.abs(trend)}% from last month
          </p>
        )}
      </div>
      <div className={`p-3 rounded-xl ${color}`}>
        <span className="text-2xl">{icon}</span>
      </div>
    </div>
  </div>
);

const QuickAction = ({ title, description, link, buttonText, icon, color }) => (
  <div className="bg-white rounded-xl shadow-sm border border-slate-200 p-6 hover:shadow-md transition-all duration-300 group">
    <div className="flex items-start space-x-4">
      <div className={`p-3 rounded-xl ${color} group-hover:scale-110 transition-transform duration-300`}>
        <span className="text-2xl">{icon}</span>
      </div>
      <div className="flex-1">
        <h3 className="font-semibold text-slate-900">{title}</h3>
        <p className="text-sm text-slate-500 mt-1">{description}</p>
        <Link
          to={link}
          className="inline-flex items-center mt-4 text-sm font-medium text-blue-600 hover:text-blue-700"
        >
          {buttonText}
          <svg className="w-4 h-4 ml-1 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 5l7 7-7 7" />
          </svg>
        </Link>
      </div>
    </div>
  </div>
);

const StatusBadge = ({ status }) => {
  const styles = {
    pending: 'bg-amber-100 text-amber-700 border-amber-200',
    processing: 'bg-blue-100 text-blue-700 border-blue-200',
    completed: 'bg-emerald-100 text-emerald-700 border-emerald-200',
    cancelled: 'bg-red-100 text-red-700 border-red-200',
    refunded: 'bg-slate-100 text-slate-700 border-slate-200',
  };

  const labels = {
    pending: 'Pending',
    processing: 'Processing',
    completed: 'Completed',
    cancelled: 'Cancelled',
    refunded: 'Refunded',
  };

  return (
    <span className={`px-3 py-1 text-xs font-medium rounded-full border ${styles[status] || 'bg-slate-100 text-slate-700'}`}>
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
      <div className="flex items-center justify-center h-64">
        <div className="flex items-center space-x-3">
          <div className="w-8 h-8 border-4 border-blue-600 border-t-transparent rounded-full animate-spin"></div>
          <span className="text-slate-500">Loading...</span>
        </div>
      </div>
    );
  }

  return (
    <div className="space-y-6">
      {/* Page header */}
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-2xl font-bold text-slate-900">Dashboard</h1>
          <p className="text-slate-500 mt-1">Welcome back! Here's your store overview.</p>
        </div>
      </div>

      {/* Stats Grid */}
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <StatCard
          title="Total Products"
          value={stats.totalProducts}
          icon="📦"
          color="bg-blue-100 text-blue-600"
          trend={12}
        />
        <StatCard
          title="Total Orders"
          value={stats.totalOrders}
          icon="🛒"
          color="bg-purple-100 text-purple-600"
          trend={8}
        />
        <StatCard
          title="Pending Orders"
          value={stats.pendingOrders}
          icon="⏳"
          color="bg-amber-100 text-amber-600"
        />
        <StatCard
          title="Completed"
          value={stats.completedOrders}
          icon="✅"
          color="bg-emerald-100 text-emerald-600"
        />
      </div>

      {/* Quick Actions */}
      <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
        <QuickAction
          title="Manage Products"
          description="Add, edit, or delete products in your catalog"
          link="/products"
          buttonText="Go to Products"
          icon="📦"
          color="bg-blue-100 text-blue-600"
        />
        <QuickAction
          title="Manage Orders"
          description="View and process customer orders"
          link="/orders"
          buttonText="Go to Orders"
          icon="🛒"
          color="bg-purple-100 text-purple-600"
        />
      </div>

      {/* Recent Orders */}
      {recentOrders.length > 0 && (
        <div className="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
          <div className="px-6 py-4 border-b border-slate-200 flex items-center justify-between">
            <h2 className="text-lg font-semibold text-slate-900">Recent Orders</h2>
            <Link to="/orders" className="text-sm font-medium text-blue-600 hover:text-blue-700">
              View all →
            </Link>
          </div>
          <div className="divide-y divide-slate-100">
            {recentOrders.map((order) => (
              <div key={order.id} className="px-6 py-4 flex items-center justify-between hover:bg-slate-50 transition-colors">
                <div className="flex items-center space-x-4">
                  <div className="w-10 h-10 bg-slate-100 rounded-full flex items-center justify-center">
                    <span className="text-slate-600">🛒</span>
                  </div>
                  <div>
                    <p className="font-medium text-slate-900">
                      #{order.attributes?.order_number || order.id}
                    </p>
                    <p className="text-sm text-slate-500">
                      {order.attributes?.items_count || 0} items • {order.attributes?.total_amount?.formatted || 'N/A'}
                    </p>
                  </div>
                </div>
                <StatusBadge status={order.attributes?.status?.value} />
              </div>
            ))}
          </div>
        </div>
      )}
    </div>
  );
};

export default Dashboard;
