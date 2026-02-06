import { useState, useEffect } from 'react';
import { productsAPI } from '../services/api';

const Products = () => {
  const [products, setProducts] = useState([]);
  const [loading, setLoading] = useState(true);
  const [showModal, setShowModal] = useState(false);
  const [editingProduct, setEditingProduct] = useState(null);
  const [searchQuery, setSearchQuery] = useState('');
  const [formData, setFormData] = useState({
    name: '',
    description: '',
    price: '',
    stock_quantity: '',
    sku: '',
    is_active: true,
  });

  useEffect(() => {
    fetchProducts();
  }, []);

  const fetchProducts = async () => {
    try {
      const response = await productsAPI.getAll();
      setProducts(response.data.data || []);
    } catch (error) {
      console.error('Error fetching products:', error);
    } finally {
      setLoading(false);
    }
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    try {
      if (editingProduct) {
        await productsAPI.update(editingProduct.id, formData);
      } else {
        await productsAPI.create(formData);
      }
      fetchProducts();
      closeModal();
    } catch (error) {
      console.error('Error saving product:', error);
      alert(error.response?.data?.error?.message || 'Error saving product');
    }
  };

  const handleDelete = async (id) => {
    if (window.confirm('Are you sure you want to delete this product?')) {
      try {
        await productsAPI.delete(id);
        fetchProducts();
      } catch (error) {
        console.error('Error deleting product:', error);
      }
    }
  };

  const openEditModal = (product) => {
    setEditingProduct(product);
    setFormData({
      name: product.attributes?.name || '',
      description: product.attributes?.description || '',
      price: product.attributes?.price?.amount || '',
      stock_quantity: product.attributes?.stock_quantity || '',
      sku: product.attributes?.sku || '',
      is_active: product.attributes?.is_active ?? true,
    });
    setShowModal(true);
  };

  const closeModal = () => {
    setShowModal(false);
    setEditingProduct(null);
    setFormData({
      name: '',
      description: '',
      price: '',
      stock_quantity: '',
      sku: '',
      is_active: true,
    });
  };

  const handleSearch = async () => {
    if (!searchQuery.trim()) {
      fetchProducts();
      return;
    }
    try {
      const response = await productsAPI.search(searchQuery);
      setProducts(response.data.data || []);
    } catch (error) {
      console.error('Error searching products:', error);
    }
  };

  if (loading) {
    return (
      <div className="d-flex align-items-center justify-content-center vh-50">
        <div className="text-center">
          <div className="spinner-border text-primary mb-3" role="status">
            <span className="visually-hidden">Loading...</span>
          </div>
          <p className="text-muted">Loading products...</p>
        </div>
      </div>
    );
  }

  return (
    <div className="container-fluid px-0">
      {/* Page header */}
      <div className="d-flex align-items-center justify-content-between mb-4">
        <div>
          <h1 className="h3 mb-1">Products</h1>
          <p className="text-muted mb-0">Manage your product catalog</p>
        </div>
        <button
          onClick={() => setShowModal(true)}
          className="btn btn-primary"
        >
          + Add Product
        </button>
      </div>

      {/* Search */}
      <div className="row g-3 mb-4">
        <div className="col-md-8">
          <div className="input-group">
            <span className="input-group-text bg-white">🔍</span>
            <input
              type="text"
              className="form-control"
              placeholder="Search products..."
              value={searchQuery}
              onChange={(e) => setSearchQuery(e.target.value)}
              onKeyPress={(e) => e.key === 'Enter' && handleSearch()}
            />
          </div>
        </div>
        <div className="col-md-4">
          <button
            onClick={handleSearch}
            className="btn btn-dark w-100"
          >
            Search
          </button>
        </div>
      </div>

      {/* Products Table */}
      <div className="card border-0 shadow-sm">
        <div className="card-body p-0">
          <div className="table-responsive">
            <table className="table table-hover mb-0">
              <thead className="table-light">
                <tr>
                  <th className="ps-4">Product</th>
                  <th>SKU</th>
                  <th>Price</th>
                  <th>Stock</th>
                  <th>Status</th>
                  <th className="text-end pe-4">Actions</th>
                </tr>
              </thead>
              <tbody>
                {products.map((product) => (
                  <tr key={product.id}>
                    <td className="ps-4">
                      <div className="d-flex align-items-center">
                        <div className="bg-light rounded-3 p-2 me-3">
                          <span>📦</span>
                        </div>
                        <div>
                          <div className="fw-medium">{product.attributes?.name}</div>
                          {product.attributes?.description && (
                            <div className="text-muted small text-truncate" style={{ maxWidth: '200px' }}>
                              {product.attributes.description}
                            </div>
                          )}
                        </div>
                      </div>
                    </td>
                    <td className="font-monospace small">{product.attributes?.sku}</td>
                    <td className="fw-medium">
                      {product.attributes?.price?.formatted || `${product.attributes?.price?.amount} EGP`}
                    </td>
                    <td>
                      <span className={`badge ${product.attributes?.stock_quantity > 10 
                        ? 'bg-success-subtle text-success' 
                        : product.attributes?.stock_quantity > 0 
                          ? 'bg-warning-subtle text-warning'
                          : 'bg-danger-subtle text-danger'
                      }`}>
                        {product.attributes?.stock_quantity} in stock
                      </span>
                    </td>
                    <td>
                      <span className={`badge ${product.attributes?.is_active ? 'bg-success' : 'bg-secondary'}`}>
                        {product.attributes?.is_active ? 'Active' : 'Inactive'}
                      </span>
                    </td>
                    <td className="text-end pe-4">
                      <button
                        onClick={() => openEditModal(product)}
                        className="btn btn-outline-primary btn-sm me-2"
                      >
                        Edit
                      </button>
                      <button
                        onClick={() => handleDelete(product.id)}
                        className="btn btn-outline-danger btn-sm"
                      >
                        Delete
                      </button>
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>

          {products.length === 0 && (
            <div className="text-center py-5">
              <div className="bg-light rounded-3 p-4 d-inline-block mb-3">
                <span style={{ fontSize: '2rem' }}>📦</span>
              </div>
              <p className="text-muted mb-0">No products found</p>
            </div>
          )}
        </div>
      </div>

      {/* Modal */}
      {showModal && (
        <div className="modal fade show d-block" style={{ zIndex: 1050 }}>
          <div className="modal-dialog modal-dialog-centered">
            <div className="modal-content shadow">
              <div className="modal-header">
                <h5 className="modal-title">
                  {editingProduct ? 'Edit Product' : 'Add New Product'}
                </h5>
                <button type="button" className="btn-close" onClick={closeModal}></button>
              </div>
              <form onSubmit={handleSubmit}>
                <div className="modal-body">
                  <div className="mb-3">
                    <label className="form-label fw-medium">Name *</label>
                    <input
                      type="text"
                      required
                      className="form-control"
                      value={formData.name}
                      onChange={(e) => setFormData({ ...formData, name: e.target.value })}
                    />
                  </div>
                  <div className="mb-3">
                    <label className="form-label">Description</label>
                    <textarea
                      className="form-control"
                      rows={3}
                      value={formData.description}
                      onChange={(e) => setFormData({ ...formData, description: e.target.value })}
                    />
                  </div>
                  <div className="row g-3 mb-3">
                    <div className="col-6">
                      <label className="form-label fw-medium">Price (EGP) *</label>
                      <input
                        type="number"
                        required
                        min="0"
                        step="0.01"
                        className="form-control"
                        value={formData.price}
                        onChange={(e) => setFormData({ ...formData, price: e.target.value })}
                      />
                    </div>
                    <div className="col-6">
                      <label className="form-label fw-medium">Stock *</label>
                      <input
                        type="number"
                        required
                        min="0"
                        className="form-control"
                        value={formData.stock_quantity}
                        onChange={(e) => setFormData({ ...formData, stock_quantity: e.target.value })}
                      />
                    </div>
                  </div>
                  <div className="mb-3">
                    <label className="form-label fw-medium">SKU *</label>
                    <input
                      type="text"
                      required
                      className="form-control font-monospace"
                      value={formData.sku}
                      onChange={(e) => setFormData({ ...formData, sku: e.target.value })}
                    />
                  </div>
                  <div className="form-check">
                    <input
                      type="checkbox"
                      className="form-check-input"
                      id="is_active"
                      checked={formData.is_active}
                      onChange={(e) => setFormData({ ...formData, is_active: e.target.checked })}
                    />
                    <label className="form-check-label" htmlFor="is_active">
                      Active
                    </label>
                  </div>
                </div>
                <div className="modal-footer">
                  <button type="button" className="btn btn-light" onClick={closeModal}>
                    Cancel
                  </button>
                  <button type="submit" className="btn btn-primary">
                    {editingProduct ? 'Update Product' : 'Create Product'}
                  </button>
                </div>
              </form>
            </div>
          </div>
          <div className="modal-backdrop fade show" onClick={closeModal}></div>
        </div>
      )}
    </div>
  );
};

export default Products;
