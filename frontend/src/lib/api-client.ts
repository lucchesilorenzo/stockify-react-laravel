import axios from "axios";
import env from "./env";
import { parseDates } from "./utils";

const api = axios.create({
  baseURL: `${env.VITE_BASE_URL}/api`,
});

api.interceptors.request.use((config) => {
  const token = localStorage.getItem("token");
  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }
  return config;
});

api.interceptors.response.use(
  (response) => {
    if (response.data.token) {
      localStorage.setItem("token", response.data.token);
    }
    response.data = parseDates(response.data);
    return response;
  },
  (error) => {
    if (axios.isAxiosError(error)) {
      if (error.response?.status === 401) {
        localStorage.removeItem("token");
      }
      throw new Error(
        error.response?.data?.message ||
          "An error occurred while making the request.",
      );
    } else {
      console.error(error.message);
      throw new Error("An unexpected error occurred.");
    }
  },
);

export async function fetchData<T>(endpoint: string): Promise<T> {
  try {
    const response = await api.get(endpoint);
    return response.data;
  } catch (error) {
    if (axios.isAxiosError(error)) {
      throw new Error(
        error.response?.data?.message ||
          "An error occurred while making the request.",
      );
    } else {
      throw new Error("An unexpected error occurred.");
    }
  }
}

export async function postData<T>(endpoint: string, data: T) {
  try {
    const response = await api.post(endpoint, data);
    return response.data;
  } catch (error) {
    if (axios.isAxiosError(error)) {
      throw new Error(
        error.response?.data?.message ||
          "An error occurred while making the request.",
      );
    } else {
      throw new Error("An unexpected error occurred.");
    }
  }
}

export async function updateData<T>(endpoint: string, data?: T) {
  try {
    const response = await api.patch(endpoint, data);
    return response.data;
  } catch (error) {
    if (axios.isAxiosError(error)) {
      throw new Error(
        error.response?.data?.message ||
          "An error occurred while making the request.",
      );
    } else {
      throw new Error("An unexpected error occurred.");
    }
  }
}

export async function deleteData(endpoint: string) {
  try {
    const response = await api.delete(endpoint);
    return response.data;
  } catch (error) {
    if (axios.isAxiosError(error)) {
      throw new Error(
        error.response?.data?.message ||
          "An error occurred while making the request.",
      );
    } else {
      throw new Error("An unexpected error occurred.");
    }
  }
}

export default api;
