import axios, { AxiosInstance } from "axios";
import env from "./env";
import { parseDates } from "./utils";
import applyCaseMiddleware from "axios-case-converter";

export const plainApi = axios.create({
  baseURL: `${env.VITE_BASE_URL}/api`,
});

const api = applyCaseMiddleware(
  axios.create({
    baseURL: `${env.VITE_BASE_URL}/api`,
  }),
);

function applyInterceptors(axiosInstance: AxiosInstance) {
  axiosInstance.interceptors.request.use((config) => {
    const token = localStorage.getItem("token");
    if (token) {
      config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
  });

  axiosInstance.interceptors.response.use(
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
        throw new Error("An unexpected error occurred.");
      }
    },
  );
}

applyInterceptors(api);
applyInterceptors(plainApi);

export async function fetchData<T>(
  endpoint: string,
  axiosInstance: AxiosInstance = api,
): Promise<T> {
  const response = await axiosInstance.get(endpoint);
  return response.data;
}

export async function postData<T>(
  endpoint: string,
  data?: T,
  axiosInstance: AxiosInstance = api,
) {
  const response = await axiosInstance.post(endpoint, data);
  return response.data;
}

export async function updateData<T>(
  endpoint: string,
  data?: T,
  axiosInstance: AxiosInstance = api,
) {
  const response = await axiosInstance.patch(endpoint, data);
  return response.data;
}

export async function deleteData(
  endpoint: string,
  axiosInstance: AxiosInstance = api,
) {
  const response = await axiosInstance.delete(endpoint);
  return response.data;
}

export default api;
