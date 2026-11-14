import api from "./api";

export const getBle = () => api.get("/ble");

export const createBle = (data) => api.post("/ble", data);

export const updateBle = async (id, data) => api.put(`/ble/${id}`, data);

export const deleteBle = (id) => api.delete(`/ble/${id}`);