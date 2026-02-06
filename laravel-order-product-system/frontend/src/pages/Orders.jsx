import { useState, useEffect } from 'react';
import { ordersAPI, productsAPI } from '../services/api';

const Orders = () => {
  const [orders, setOrders] = useState([]);
  const [loading, setLoading] = useState(true);
  const [showModal, setShowModal] = useState(false);
  const [selectedOrder, setSelectedOrder] = useState(null);
  const [statusFilter, setStatusFilter] = useState('');
  const [products, setProducts] = useState([]);
  const [formData, setFormData] = useState({
    customer_id: 1,
    notes: '',
    items: [{ product_id: '', quantity: 1 }],
  });

  useEffect(() => {
    fetchOrders();
    fetchProducts();
  }, [statusFilter]);

  const fetchOrders = async () => {
    try {
      const response = statusFilter
        ? await ordersAPI.getByStatus(statusFilter)
        : await ordersAPI.getAll();
      setOrders(response.data.data || []);
    } catch (error) {
      console.error('Error fetching orders:', error);
    } finally {
      setLoading(false);
    }
  };

  const fetchProducts = async () => {
    try {
      const response = await productsAPI.getAll();
      setProducts(response.data.data || []);
    } catch (error) {
      console.error('Error fetching products:', error);
    }
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    try {
      await ordersAPI.create(formData);
      fetchOrders();
      closeModal();
    } catch (error) {
      console.error('Error creating order:', error);
      alert(error.response?.data?.error?.message || 'Error creating order');
    }
  };

  const handleStatusChange = async (orderId, newStatus) => {
    try {
      await ordersAPI.updateStatus(orderId, { status: newStatus });
      fetchOrders();
    } catch (error) {
      console.error('Error updating status:', error);
    }
  };

  const handleCancel = async (orderId) => {
    if (window.confirm('Are you sure you want to cancel this order?')) {
      try {
        await ordersAPI.cancel(orderId);
        fetchOrders();
      } catch (error) {
        console.error('Error cancelling order:', error);
      }
    }
  };

  const handleDelete = async (orderId) => {
    if (window.confirm('Are you sure you want to delete this order?')) {
      try {
        await ordersAPI.delete(orderId);
        fetchOrders();
      } catch (error) {
        console.error('Error deleting order:', error);
      }
    }
  };

  const viewOrderDetails = (order) => {
    setSelectedOrder(order);
  };

  const closeModal = () => {
    setShowModal(false);
    setSelectedOrder(null);
    setFormData({
      customer_id: 1,
      notes: '',
      items: [{ product_id: '', quantity: 1 }],
    });
  };

  const addItem = () => {
    setFormData({
      ...formData,
      items: [...formData.items, { product_id: '', quantity: 1 }],
    });
  };

  const removeItem = (index) => {
    if (formData.items.length > 1) {
      setFormData({
        ...formData,
        items: formData.items.filter((_, i) => i !== index),
      });
    }
  };

  const updateItem = (index, field, value) => {
    const newItems = [...formData.items];
    newItems[index] = { ...newItems[index], [field]: value };
    setFormData({ ...formData, items: newItems });
  };

  const getStatusBadge = (status) => {
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

  if (loading) {
    return (
      <div className="flex items-center justify-center h-64">
        <div className="flex items-center space-x-3">
          <div className="w-8 h-8 border-4 border-blue-600 border-t-transparent rounded-full animate-spin"></div>
          <span className="text-slate-500">Loading orders...</span>
        </div>
      </div>
    );
  }

  return (
    <div className="space-y-6">
      {/* Page header */}
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-2xl font-bold text-slate-900">Orders</h1>
          <p className="text-slate-500 mt-1">Manage and track customer orders</p>
        </div>
        <button
          onClick={() => setShowModal(true)}
          className="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors shadow-sm shadow-blue-600/30"
        >
          <svg className="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 4v16m8-8H4" />
          </svg>
          New Order
        </button>
      </div>

      {/* Filters */}
      <div className="flex gap-3">
        <select
          value={statusFilter}
          onChange={(e) => setStatusFilter(e.target.value)}
          className="px-4 py-2.5 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white"
        >
          <option value="">All Orders</option>
          <option value="pending">Pending</option>
          <option value="processing">Processing</option>
          <option value="completed">Completed</option>
          <option value="cancelled">Cancelled</option>
        </select>
      </div>

      {/* Orders Table */}
      <div className="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <table className="min-w-full divide-y divide-slate-200">
          <thead className="bg-slate-50">
            <tr>
              <th className="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">
                Order #
              </th>
              <th className="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">
                Items
              </th>
              <th className="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">
                Total
              </th>
              <th className="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">
                Status
              </th>
              <th className="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">
                Date
              </th>
              <th className="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase tracking-wider">
                Actions
              </th>
            </tr>
          </thead>
          <tbody className="bg-white divide-y divide-slate-200">
            {orders.map((order) => (
              <tr key={order.id} className="hover:bg-slate-50 transition-colors">
                <td className="px-6 py-4 whitespace-nowrap">
                  <div className="flex items-center">
                    <div className="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center">
                      <span className="text-purple-600">🛒</span>
                    </div>
                    <div className="ml-4">
                      <div className="text-sm font-medium text-slate-900">
                        #{order.attributes?.order_number || order.id}
                      </div>
                      <div className="text-sm text-slate-500">
                        Customer: {order.attributes?.customer_id}
                      </div>
                    </div>
                  </div>
                </td>
                <td className="px-6 py-4 whitespace-nowrap text-sm text-slate-600">
                  {order.attributes?.items_count || 0} items
                </td>
                <td className="px-6 py-4 whitespace-nowrap text-sm font-semibold text-slate-900">
                  {order.attributes?.total_amount?.formatted || 'N/A'}
                </td>
                <td className="px-6 py-4 whitespace-nowrap">
                  {getStatusBadge(order.attributes?.status?.value)}
                </td>
                <td className="px-6 py-4 whitespace-nowrap text-sm text-slate-500">
                  {order.meta?.created_at
                    ? new Date(order.meta.created_at).toLocaleDateString()
                    : 'N/A'}
                </td>
                <td className="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                  <button
                    onClick={() => viewOrderDetails(order)}
                    className="text-blue-600 hover:text-blue-900 mr-3 transition-colors"
                  >
                    View
                  </button>
                  {order.attributes?.status?.value === 'pending' && (
                    <button
                      onClick={() => handleStatusChange(order.id, 'processing')}
                      className="text-amber-600 hover:text-amber-900 mr-3 transition-colors"
                    >
                      Process
                    </button>
                  )}
                  {['pending', 'processing'].includes(order.attributes?.status?.value) && (
                    <button
                      onClick={() => handleStatusChange(order.id, 'completed')}
                      className="text-emerald-600 hover:text-emerald-900 mr-3 transition-colors"
                    >
                      Complete
                    </button>
                  )}
                  {order.attributes?.is_cancellable && (
                    <button
                      onClick={() => handleCancel(order.id)}
                      className="text-red-600 hover:text-red-900 mr-3 transition-colors"
                    >
                      Cancel
                    </button>
                  )}
                  <button
                    onClick={() => handleDelete(order.id)}
                    className="text-slate-600 hover:text-slate-900 transition-colors"
                  >
                    Delete
                  </button>
                </td>
              </tr>
            ))}
          </tbody>
        </table>

        {orders.length === 0 && (
          <div className="text-center py-12">
            <div className="w-16 h-16 mx-auto bg-slate-100 rounded-full flex items-center justify-center mb-4">
              <span className="text-3xl">🛒</span>
            </div>
            <p className="text-slate-500">No orders found</p>
          </div>
        )}
      </div>

      {/* Create Order Modal */}
      {showModal && (
        <div className="fixed inset-0 bg-slate-900/50 flex items-center justify-center z-50 p-4 overflow-y-auto">
          <div className="bg-white rounded-2xl shadow-xl w-full max-w-2xl my-8">
            <div className="px-6 py-4 border-b border-slate-200 flex items-center justify-between">
              <h3 className="text-lg font-semibold text-slate-900">Create New Order</h3>
              <button
                onClick={closeModal}
                className="text-slate-400 hover:text-slate-600 transition-colors"
              >
                ✕
              </button>
            </div>
            <form onSubmit={handleSubmit} className="p-6">
              <div className="space-y-4">
                <div>
                  <label className="block text-sm font-medium text-slate-700 mb-1">
                    Customer ID *
                  </label>
                  <input
                    type="number"
                    required
                    value={formData.customer_id}
                    onChange={(e) => setFormData({ ...formData, customer_id: parseInt(e.target.value) })}
                    className="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                  />
                </div>

                <div>
                  <label className="block text-sm font-medium text-slate-700 mb-2">
                    Order Items *
                  </label>
                  {formData.items.map((item, index) => (
                    <div key={index} className="flex gap-2 mb-2">
                      <select
                        value={item.product_id}
                        onChange={(e) => updateItem(index, 'product_id', e.target.value)}
                        required
                        className="flex-1 px-4 py-2.5 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                      >
                        <option value="">Select Product</option>
                        {products.map((product) => (
                          <option key={product.id} value={product.id}>
                            {product.attributes?.name} - {product.attributes?.price?.formatted}
                          </option>
                        ))}
                      </select>
                      <input
                        type="number"
                        min="1"
                        value={item.quantity}
                        onChange={(e) => updateItem(index, 'quantity', parseInt(e.target.value))}
                        required
                        className="w-24 px-4 py-2.5 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        placeholder="Qty"
                      />
                      {formData.items.length > 1 && (
                        <button
                          type="button"
                          onClick={() => removeItem(index)}
                          className="px-3 py-2 text-red-600 hover:text-red-800 transition-colors"
                        >
                          ✕
                        </button>
                      )}
                    </div>
                  ))}
                  <button
                    type="button"
                    onClick={addItem}
                    className="text-blue-600 hover:text-blue-800 text-sm font-medium transition-colors"
                  >
                    + Add Another Item
                  </button>
                </div>

                <div>
                  <label className="block text-sm font-medium text-slate-700 mb-1">
                    Notes
                  </label>
                  <textarea
                    value={formData.notes}
                    onChange={(e) => setFormData({ ...formData, notes: e.target.value })}
                    className="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    rows={2}
                  />
                </div>
              </div>

              <div className="flex justify-end gap-3 mt-6">
                <button
                  type="button"
                  onClick={closeModal}
                  className="px-4 py-2.5 text-slate-700 bg-slate-100 rounded-lg hover:bg-slate-200 transition-colors"
                >
                  Cancel
                </button>
                <button
                  type="submit"
                  className="px-4 py-2.5 text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors shadow-sm shadow-blue-600/30"
                >
                  Create Order
                </button>
              </div>
            </form>
          </div>
        </div>
      )}

      {/* Order Details Modal */}
      {selectedOrder && (
        <div className="fixed inset-0 bg-slate-900/50 flex items-center justify-center z-50 p-4">
          <div className="bg-white rounded-2xl shadow-xl w-full max-w-2xl max-h-[90vh] overflow-hidden">
            <div className="px-6 py-4 border-b border-slate-200 flex items-center justify-between">
              <h3 className="text-lg font-semibold text-slate-900">
                Order #{selectedOrder.attributes?.order_number || selectedOrder.id}
              </h3>
              <button
                onClick={() => setSelectedOrder(null)}
                className="text-slate-400 hover:text-slate-600 transition-colors"
              >
                ✕
              </button>
            </div>

            <div className="p-6 overflow-y-auto max-h-[calc(90vh-120px)]">
              <div className="grid grid-cols-2 gap-4 mb-6">
                <div>
                  <p className="text-sm text-slate-500">Status</p>
                  <p className="font-medium mt-1">{getStatusBadge(selectedOrder.attributes?.status?.value)}</p>
                </div>
                <div>
                  <p className="text-sm text-slate-500">Total</p>
                  <p className="font-medium text-lg text-slate-900 mt-1">
                    {selectedOrder.attributes?.total_amount?.formatted || 'N/A'}
                  </p>
                </div>
                <div>
                  <p className="text-sm text-slate-500">Customer ID</p>
                  <p className="font-medium text-slate-900 mt-1">{selectedOrder.attributes?.customer_id}</p>
                </div>
                <div>
                  <p className="text-sm text-slate-500">Date</p>
                  <p className="font-medium text-slate-900 mt-1">
                    {selectedOrder.meta?.created_at
                      ? new Date(selectedOrder.meta.created_at).toLocaleString()
                      : 'N/A'}
                  </p>
                </div>
              </div>

              {selectedOrder.attributes?.notes && (
                <div className="mb-6">
                  <p className="text-sm text-slate-500">Notes</p>
                  <p className="font-medium text-slate-900 mt-1">{selectedOrder.attributes.notes}</p>
                </div>
              )}

              <div>
                <p className="text-sm text-slate-500 mb-3">Items</p>
                <div className="bg-slate-50 rounded-xl p-4 space-y-3">
                  {selectedOrder.attributes?.items?.map((item, index) => (
                    <div key={index} className="flex justify-between py-2 border-b border-slate-200 last:border-0">
                      <div>
                        <p className="font-medium text-slate-900">
                          {item.attributes?.product_name || `Product #${item.product_id}`}
                        </p>
                        <p className="text-sm text-slate-500">
                          {item.attributes?.quantity} x {item.attributes?.unit_price?.formatted || `${item.unit_price} EGP`}
                        </p>
                      </div>
                      <p className="font-semibold text-slate-900">
                        {item.attributes?.subtotal?.formatted || `${item.subtotal} EGP`}
                      </p>
                    </div>
                  ))}
                </div>
              </div>
            </div>
          </div>
        </div>
      )}
    </div>
  );
};

export default Orders;
