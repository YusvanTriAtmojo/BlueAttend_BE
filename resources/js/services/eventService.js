import api from "./api";

export const getEvent = () => api.get("/event");

export const createEvent = (data) => api.post("/event", data);

export const updateEvent = async (id, data) => api.put(`/event/${id}`, data);

export const deleteEvent = (id) => api.delete(`/event/${id}`);