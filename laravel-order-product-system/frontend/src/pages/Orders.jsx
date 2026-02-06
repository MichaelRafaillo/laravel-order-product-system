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

  if (loading) {
    return (
      <div className="d-flex align-items-center justify-content-center vh-50">
        <div className="text-center">
          <div className="spinner-border text-primary mb-3" role="status">
            <span className="visually-hidden">Loading...</span>
          </div>
          <p className="text-muted">Loading orders...</p>
        </div>
      </div>
    );
  }

  return (
    <div className="container-fluid px-0">
      {/* Page header */}
      <div className="d-flex align-items-center justify-content-between mb-4">
        <div>
          <h1 className="h3 mb-1">Orders</h1>
          <p className="text-muted mb-0">Manage and track customer orders</p>
        </div>
        <button
          onClick={() => setShowModal(true)}
          className="btn btn-primary"
        >
          + New Order
        </button>
      </div>

      {/* Filters */}
      <div className="mb-4">
        <select
          value={statusFilter}
          onChange={(e) => setStatusFilter(e.target.value)}
          className="form-select"
          style={{ width: 'auto' }}
        >
          <option value="">All Orders</option>
          <option value="pending">Pending</option>
          <option value="processing">Processing</option>
          <option value="completed">Completed</option>
          <option value="cancelled">Cancelled</option>
        </select>
      </div>

      {/* Orders Table */}
      <div className="card border-0 shadow-sm">
        <div className="card-body p-0">
          <div className="table-responsive">
            <table className="table table-hover mb-0">
              <thead className="table-light">
                <tr>
                  <th className="ps-4">Order #</th>
                  <th>Items</th>
                  <th>Total</th>
                  <th>Status</th>
                  <th>Date</th>
                  <th className="text-end pe-4">Actions</th>
                </tr>
              </thead>
              <tbody>
                {orders.map((order) => (
                  <tr key={order.id}>
                    <td className="ps-4">
                      <div className="d-flex align-items-center">
                        <div className="bg-primary bg-opacity-10 rounded-circle p-2 me-3">
                          <span>🛒</span>
                        </div>
                        <div>
                          <div className="fw-medium">
                            #{order.attributes?.order_number || order.id}
                          </div>
                          <div className="text-muted small">
                            Customer: {order.attributes?.customer_id}
                          </div>
                        </div>
                      </div>
                    </td>
                    <td>{order.attributes?.items_count || 0} items</td>
                    <td className="fw-medium">
                      {order.attributes?.total_amount?.formatted || 'N/A'}
                    </td>
                    <td>{getStatusBadge(order.attributes?.status?.value)}</td>
                    <td className="text-muted">
                      {order.meta?.created_at
                        ? new Date(order.meta.created_at).toLocaleDateString()
                        : 'N/A'}
                    </td>
                    <td className="text-end pe-4">
                      <button
                        onClick={() => viewOrderDetails(order)}
                        className="btn btn-outline-primary btn-sm me-2"
                      >
                        View
                      </button>
                      {order.attributes?.status?.value === 'pending' && (
                        <button
                          onClick={() => handleStatusChange(order.id, 'processing')}
                          className="btn btn-outline-warning btn-sm me-2"
                        >
                          Process
                        </button>
                      )}
                      {['pending', 'processing'].includes(order.attributes?.status?.value) && (
                        <button
                          onClick={() => handleStatusChange(order.id, 'completed')}
                          className="btn btn-outline-success btn-sm me-2"
                        >
                          Complete
                        </button>
                      )}
                      {order.attributes?.is_cancellable && (
                        <button
                          onClick={() => handleCancel(order.id)}
                          className="btn btn-outline-danger btn-sm me-2"
                        >
                          Cancel
                        </button>
                      )}
                      <button
                        onClick={() => handleDelete(order.id)}
                        className="btn btn-outline-secondary btn-sm"
                      >
                        Delete
                      </button>
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>

          {orders.length === 0 && (
            <div className="text-center py-5">
              <div className="bg-light rounded-3 p-4 d-inline-block mb-3">
                <span style={{ fontSize: '2rem' }}>🛒</span>
              </div>
              <p className="text-muted mb-0">No orders found</p>
            </div>
          )}
        </div>
      </div>

      {/* Create Order Modal */}
      {showModal && (
        <div className="modal fade show d-block" style={{ zIndex: 1050 }}>
          <div className="modal-dialog modal-dialog-centered modal-lg">
            <div className="modal-content shadow">
              <div className="modal-header">
                <h5 className="modal-title">Create New Order</h5>
                <button type="button" className="btn-close" onClick={closeModal}></button>
              </div>
              <form onSubmit={handleSubmit}>
                <div className="modal-body">
                  <div className="mb-3">
                    <label className="form-label fw-medium">Customer ID *</label>
                    <input
                      type="number"
                      required
                      className="form-control"
                      value={formData.customer_id}
                      onChange={(e) => setFormData({ ...formData, customer_id: parseInt(e.target.value) })}
                    />
                  </div>

                  <div className="mb-3">
                    <label className="form-label fw-medium">Order Items *</label>
                    {formData.items.map((item, index) => (
                      <div key={index} className="row g-2 mb-2">
                        <div className="col-7">
                          <select
                            value={item.product_id}
                            onChange={(e) => updateItem(index, 'product_id', e.target.value)}
                            required
                            className="form-select"
                          >
                            <option value="">Select Product</option>
                            {products.map((product) => (
                              <option key={product.id} value={product.id}>
                                {product.attributes?.name} - {product.attributes?.price?.formatted}
                              </option>
                            ))}
                          </select>
                        </div>
                        <div className="col-3">
                          <input
                            type="number"
                            min="1"
                            value={item.quantity}
                            onChange={(e) => updateItem(index, 'quantity', parseInt(e.target.value))}
                            required
                            className="form-control"
                            placeholder="Qty"
                          />
                        </div>
                        <div className="col-2">
                          {formData.items.length > 1 && (
                            <button
                              type="button"
                              onClick={() => removeItem(index)}
                              className="btn btn-outline-danger"
                            >
                              ✕
                            </button>
                          )}
                        </div>
                      </div>
                    ))}
                    <button
                      type="button"
                      onClick={addItem}
                      className="btn btn-link p-0"
                    >
                      + Add Another Item
                    </button>
                  </div>

                  <div className="mb-3">
                    <label className="form-label">Notes</label>
                    <textarea
                      className="form-control"
                      rows={2}
                      value={formData.notes}
                      onChange={(e) => setFormData({ ...formData, notes: e.target.value })}
                    />
                  </div>
                </div>
                <div className="modal-footer">
                  <button type="button" className="btn btn-light" onClick={closeModal}>
                    Cancel
                  </button>
                  <button type="submit" className="btn btn-primary">
                    Create Order
                  </button>
                </div>
              </form>
            </div>
          </div>
          <div className="modal-backdrop fade show" onClick={closeModal}></div>
        </div>
      )}

      {/* Order Details Modal */}
      {selectedOrder && (
        <div className="modal fade show d-block" style={{ zIndex: 1050 }}>
          <div className="modal-dialog modal-dialog-centered">
            <div className="modal-content shadow">
              <div className="modal-header">
                <h5 className="modal-title">
                  Order #{selectedOrder.attributes?.order_number || selectedOrder.id}
                </h5>
                <button type="button" className="btn-close" onClick={() => setSelectedOrder(null)}></button>
              </div>
              <div className="modal-body">
                <div className="row g-3 mb-4">
                  <div className="col-6">
                    <p className="text-muted small mb-1">Status</p>
                    <p className="mb-0">{getStatusBadge(selectedOrder.attributes?.status?.value)}</p>
                  </div>
                  <div className="col-6">
                    <p className="text-muted small mb-1">Total</p>
                    <p className="fw-bold fs-5 mb-0">
                      {selectedOrder.attributes?.total_amount?.formatted || 'N/A'}
                    </p>
                  </div>
                  <div className="col-6">
                    <p className="text-muted small mb-1">Customer ID</p>
                    <p className="mb-0">{selectedOrder.attributes?.customer_id}</p>
                  </div>
                  <div className="col-6">
                    <p className="text-muted small mb-1">Date</p>
                    <p className="mb-0">
                      {selectedOrder.meta?.created_at
                        ? new Date(selectedOrder.meta.created_at).toLocaleString()
                        : 'N/A'}
                    </p>
                  </div>
                </div>

                {selectedOrder.attributes?.notes && (
                  <div className="mb-4">
                    <p className="text-muted small mb-1">Notes</p>
                    <p className="mb-0">{selectedOrder.attributes.notes}</p>
                  </div>
                )}

                <div>
                  <p className="text-muted small mb-3">Items</p>
                  <div className="bg-light rounded-3 p-3">
                    {selectedOrder.attributes?.items?.map((item, index) => (
                      <div key={index} className="d-flex justify-content-between py-2 border-bottom">
                        <div>
                          <div className="fw-medium">
                            {item.attributes?.product_name || `Product #${item.product_id}`}
                          </div>
                          <div className="text-muted small">
                            {item.attributes?.quantity} × {item.attributes?.unit_price?.formatted || `${item.unit_price} EGP`}
                          </div>
                        </div>
                        <div className="fw-medium">
                          {item.attributes?.subtotal?.formatted || `${item.subtotal} EGP`}
                        </div>
                      </div>
                    ))}
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div className="modal-backdrop fade show" onClick={() => setSelectedOrder(null)}></div>
        </div>
      )}
    </div>
  );
};

export default Orders;
