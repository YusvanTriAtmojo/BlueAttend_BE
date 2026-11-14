import api from "./api";

export const getUser = () => api.get("/peserta");

export const createUser = (data) => api.post("/peserta", data);

export const updateUser = async (id, data) => api.put(`/peserta/${id}`, data);

export const deleteUser = (id) => api.delete(`/peserta/${id}`);