from sentence_transformers import SentenceTransformer
import numpy as np

from huggingface_hub import snapshot_download

class EmbeddingServiceInterface:
    def embed_text(self, text: str) -> list[float]:
        raise NotImplementedError

class SentenceTransformerEmbedder(EmbeddingServiceInterface):
    def __init__(self, model_name: str = 'all-MiniLM-L6-v2'):
        # Optimize download by ignoring ALL unused formats (ONNX, OpenVINO, PyTorch bin)
        # We only need safetensors and config files for SentenceTransformer.
        model_path = snapshot_download(
            repo_id="sentence-transformers/" + model_name if "/" not in model_name else model_name,
            ignore_patterns=["*.onnx", "*.ot", "*.h5", "*.msgpack", "openvino*"]
        )
        self.model = SentenceTransformer(model_path)
        
    def embed_text(self, text: str) -> list[float]:
        embedding = self.model.encode(text)
        return embedding.tolist()
